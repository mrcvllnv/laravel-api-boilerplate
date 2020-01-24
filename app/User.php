<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'verification_code', 'verification_code_expires_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'verification_code_expires_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Set the user's password
     *
     * @param string $password
     * @return void
     */
    public function setPasswordAttribute(string $password)
    {
        if (! empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    /**
     * Set the user's verification code
     *
     * @param string $password
     * @return void
     */
    public function setVerificationCodeAttribute(string $code)
    {
        if (! empty($code)) {
            $this->attributes['verification_code'] = bcrypt($code);
        }
    }

    /**
     * Check if the verification code is expired
     *
     * @return boolean
     */
    public function isVerificationCodeExpired(): bool
    {
        return $this->verification_code_expires_at
        ? $this->verification_code_expires_at->diffInMinutes() >= 60
        : false;
    }

    /**
     * Send the verification code notification.
     *
     * @return boolean
     */
    public function sendVerificationCodeViaEmail(): bool
    {
        $code = mt_rand(1000, 9999);

        $this->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addHour()
        ]);

        $this->notify(new VerificationCodeNotification($code));

        return true;
    }
}
