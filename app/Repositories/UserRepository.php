<?php

namespace App\Repositories;

use App\Exceptions\AlreadyVerifiedException;
use App\Exceptions\ExpiredVerificationCodeException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\InvalidResetTokenException;
use App\Exceptions\InvalidVerificationCodeException;
use App\Exceptions\SignUpException;
use App\Exceptions\UserNotFoundException;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerificationCodeNotification;
use App\Notifications\VerifiedEmailNotification;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new repository instance.
     *
     * @param  \App\Models\User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user by email address.
     *
     * @param  string  $email
     * @return \App\Models\User
     */
    public function getByEmail(string $email): User
    {
        return $this->user->whereEmail($email)->firstOrFail();
    }

    /**
     * Send verification code notification.
     *
     * @param  \App\Models\User  $user
     * @return boolean
     */
    public function sendVerificationCode(User $user): bool
    {
        $code = mt_rand(1000, 9999);

        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addHour()
        ]);

        $user->notify(new VerificationCodeNotification($code));

        return boolval(true);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  array  $attributes
     * @return \App\Models\User
     */
    public function register(array $attributes): User
    {
        $user = DB::transaction(function () use ($attributes) {
            try {
                $user = $this->user->create($attributes);
                
                $this->sendVerificationCode($user);
            } catch (\Throwable $th) {
                throw new SignUpException();
            }

            return $user;
        });

        return $user;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  string  $email
     * @param  string  $password
     * @return \App\Models\User
     */
    public function login(string $email, string $password): User
    {
        try {
            $user = $this->getByEmail($email);
        } catch (\Throwable $th) {
            throw new InvalidCredentialsException(trans('auth.failed'));
        }

        $isPasswordWrong = !Hash::check($password, $user->password);
        throw_if($isPasswordWrong, new InvalidCredentialsException(trans('auth.failed')));

        return $user;
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \App\Models\User  $user
     * @param  integer  $code
     * @return boolean
     */
    public function verifyEmail(User $user, int $code): bool
    {
        if ($user->hasVerifiedEmail()) {
            throw new AlreadyVerifiedException;
        }

        if (! Hash::check($code, $user->verification_code)) {
            throw new InvalidVerificationCodeException;
        }

        if ($user->isVerificationCodeExpired()) {
            throw new ExpiredVerificationCodeException;
        }

        $result = $user->markEmailAsVerified();

        if ($result) {
            $user->notify(new VerifiedEmailNotification($user));
        }

        return boolval($result);
    }

    /**
     * Resend the email verification code notification.
     *
     * @param  \App\Models\User  $user
     * @return boolean
     */
    public function resendVerificationCode(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            throw new AlreadyVerifiedException;
        }

        $result = $this->sendVerificationCode($user);

        return boolval($result);
    }

    /**
     * Send a password reset link to a user.
     *
     * @param string $email
     * @return boolean
     */
    public function sendResetLink(string $email): bool
    {
        try {
            $user = $this->getByEmail($email);
        } catch (\Throwable $th) {
            throw new UserNotFoundException;
        }

        $token = urlencode(Hash::make(Str::random(40)));

        $result = DB::table(config('auth.passwords.users.table'))->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => now(),
        ]);

        if ($result) {
            $domain = trim(config('auth.passwords.reset_domain'), '/');
            $path = config('auth.passwords.reset_path');
            $url = "$domain/$path/$token";
            
            $user->notify(new ResetPasswordNotification($url));
        }

        return boolval($result);
    }

    /**
     * Reset the password for the given token.
     *
     * @param string $token
     * @param string $password
     * @return boolean
     */
    public function resetPassword(string $token, string $password): bool
    {
        try {
            $reset = DB::table(config('auth.passwords.users.table'))->whereToken($token)->first();
        } catch (\Throwable $th) {
            throw new InvalidResetTokenException;
        }

        try {
            $user = $this->getByEmail($reset->email);
        } catch (\Throwable $th) {
            throw new UserNotFoundException;
        }

        $result = $user->update([
            'password' => $password
        ]);

        if ($result) {
            DB::table(config('auth.passwords.users.table'))->whereToken($token)->delete();
        }

        return boolval($result);
    }
}