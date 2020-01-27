<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccessTokenResource;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
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
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return AccessTokenResource
     */
    public function __invoke(LoginRequest $request): AccessTokenResource
    {
        try {
            $user = User::whereEmail($request->email)->firstOrfail();
        } catch (\Throwable $th) {
            throw new InvalidCredentialsException(trans('auth.failed'));
        }

        $isPasswordWrong = !Hash::check($request->password, $user->password);
        throw_if($isPasswordWrong, new InvalidCredentialsException(trans('auth.failed')));

        return new AccessTokenResource($user);
    }
}
