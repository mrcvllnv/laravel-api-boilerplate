<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Exceptions\SignUpException;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\RegistrationRequest;
use App\Notifications\VerificationCodeNotification;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Register user
     *
     * @param RegistrationRequest $request
     * @return User
     */
    public function __invoke(RegistrationRequest $request): User
    {
         $user = DB::transaction(function () use ($request) {
            try {
                $user = User::create($request->validated());
                $isSendingSuccessful = $this->sendVerificationCodeViaEmail($user);

                if (!$isSendingSuccessful) {
                    throw new SignUpException();
                }
            } catch (QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    throw new SignUpException($e->getMessage(), 400, $e);
                }
                throw new SignUpException();
            } catch (\Throwable $th) {
                throw new SignUpException();
            }

            return $user;
        });

        return $user;
    }

    /**
     * Send verification code via email
     *
     * @param User $user
     * @return boolean
     */
    private function sendVerificationCodeViaEmail(User $user): bool
    {
        $code = mt_rand(1000, 9999);
        $user->confirmation_code = $code;

        if ($user->save()) {
            $user->notify(new VerificationCodeNotification($code));

            return true;
        }
        
        return false;
    }
}
