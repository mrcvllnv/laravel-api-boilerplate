<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Exceptions\SignUpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
                
                $user->sendVerificationCodeViaEmail();
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
}
