<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\BooleanResource;
use App\Http\Requests\EmailVerificationRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

final class VerificationHandler extends Controller
{
    /**
     * The user repository instance.
     *
     * @var \App\Repositories\Interfaces\UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\Interfaces\UserRepositoryInterface  $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;

        $this->middleware('auth');
        $this->middleware('throttle:6,1');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \App\Http\Requests\EmailVerificationRequest  $request
     * @return \App\Http\Resources\BooleanResource
     */
    public function verify(EmailVerificationRequest $request): BooleanResource
    {
        $result = $this->userRepository->verifyEmail($request->user(), $request->code);

        return new BooleanResource($result);
    }

    /**
     * Resend the email verification code notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\BooleanResource
     */
    public function resend(Request $request): BooleanResource
    {
        $result = $this->userRepository->resendVerificationCode($request->user());

        return new BooleanResource($result);
    }
}
