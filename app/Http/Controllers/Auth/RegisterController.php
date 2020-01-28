<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\AccessTokenResource;
use App\Repositories\Interfaces\UserRepositoryInterface;

final class RegisterController extends Controller
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

        $this->middleware('guest');
    }
    
    /**
     * Handle user registration
     *
     * @param  \App\Http\Requests\RegistrationRequest  $request
     * @return \App\Http\Resources\AccessTokenResource
     */
    public function __invoke(RegistrationRequest $request): AccessTokenResource
    {
        $user = $this->userRepository->register($request->validated());

        return new AccessTokenResource($user);
    }
}
