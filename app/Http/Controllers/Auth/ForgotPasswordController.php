<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Exceptions\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\BooleanResource;
use App\Http\Requests\ForgotPasswordRequest;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Send a reset link to the given user.
     *
     * @param ForgotPasswordRequest $request
     * @return BooleanResource
     */
    public function __invoke(ForgotPasswordRequest $request): BooleanResource
    {
        try {
            $user = User::whereEmail($request->email)->firstOrfail();
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

        return new BooleanResource($result);
    }
}
