<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
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
        $this->middleware('guest')->except('logout');
    }

    public function __invoke(LoginRequest $request)
    {
        try {
            $user = User::whereEmail($request->email)->firstOrfail();
        } catch (\Throwable $th) {
            throw new InvalidCredentialsException(trans('auth.failed'));
        }

        $isPasswordWrong = !Hash::check($request->password, $user->password);
        throw_if($isPasswordWrong, new InvalidCredentialsException(trans('auth.failed')));

        return $this->respondWithToken($user);
    }

    protected function respondWithToken($user)
    {
        return response()->json([
            'access_token' => auth()->login($user),
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
