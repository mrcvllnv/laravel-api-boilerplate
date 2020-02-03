<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Resources\BooleanResource;
use App\Repositories\Interfaces\UserRepositoryInterface;

final class ForgotPasswordHandler extends Controller
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
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \App\Http\Requests\ForgotPasswordRequest  $request
     * @return \App\Http\Resources\BooleanResource
     */
    public function __invoke(ForgotPasswordRequest $request): BooleanResource
    {
        $result = $this->userRepository->sendResetLink($request->email);

        return new BooleanResource($result);
    }
}
