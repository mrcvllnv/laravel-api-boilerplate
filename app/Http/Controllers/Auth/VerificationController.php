<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\BooleanResource;
use App\Exceptions\AlreadyVerifiedException;
use App\Http\Requests\EmailVerificationRequest;
use App\Notifications\VerifiedEmailNotification;
use App\Exceptions\ExpiredVerificationCodeException;
use App\Exceptions\InvalidVerificationCodeException;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:6,1');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param EmailVerificationRequest $request
     * @return JsonResponse
     */
    public function verify(EmailVerificationRequest $request): BooleanResource
    {
        if ($request->user()->hasVerifiedEmail()) {
            throw new AlreadyVerifiedException;
        }

        if (! Hash::check($request->code, $request->user()->verification_code)) {
            throw new InvalidVerificationCodeException;
        }

        if ($request->user()->isVerificationCodeExpired()) {
            throw new ExpiredVerificationCodeException;
        }

        $result = $request->user()->markEmailAsVerified();

        if ($result) {
            $request->user()->notify(new VerifiedEmailNotification($request->user()));
        }

        return new BooleanResource($result);
    }

    /**
     * Resend the email verification code notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            throw new AlreadyVerifiedException;
        }

        return new BooleanResource($request->user()->sendVerificationCodeViaEmail());
    }
}
