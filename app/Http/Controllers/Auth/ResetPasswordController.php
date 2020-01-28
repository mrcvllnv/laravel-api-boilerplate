<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\BooleanResource;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;

final class ResetPasswordController extends Controller
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
     * Reset the given user's password.
     *
     * @param  \App\Http\Requests\ResetPasswordRequest  $request
     * @return \App\Http\Resources\BooleanResource
     */
    public function __invoke(ResetPasswordRequest $request): BooleanResource
    {
        $result = $this->userRepository->resetPassword($request->reset_token, $request->password);

        return new BooleanResource($result);
    }
}
