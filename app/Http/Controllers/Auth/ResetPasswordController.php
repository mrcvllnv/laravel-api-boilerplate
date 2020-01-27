<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\BooleanResource;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\ResetPasswordRequest;
use App\Exceptions\InvalidResetTokenException;

class ResetPasswordController extends Controller
{
    /**
     * Reset the given user's password.
     *
     * @param ResetPasswordRequest $request
     * @return BooleanResource
     */
    public function __invoke(ResetPasswordRequest $request): BooleanResource
    {
        try {
            $reset = DB::table(config('auth.passwords.users.table'))->whereToken($request->reset_token)->first();
        } catch (\Throwable $th) {
            throw new InvalidResetTokenException;
        }

        try {
            $user = User::whereEmail($reset->email)->first();
        } catch (\Throwable $th) {
            throw new UserNotFoundException;
        }

        $result = $user->update([
            'password' => $request->password
        ]);

        if ($result) {
            DB::table(config('auth.passwords.users.table'))->whereToken($request->reset_token)->delete();
        }

        return new BooleanResource($result);
    }
}
