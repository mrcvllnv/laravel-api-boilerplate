<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AccessTokenResource;
use App\Repositories\Interfaces\UserRepositoryInterface;

final class LoginController extends Controller
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
     * Handle a login request to the application.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \App\Http\Resources\AccessTokenResource
     */
    public function __invoke(LoginRequest $request): AccessTokenResource
    {
        $user = $this->userRepository->login($request->email, $request->password);

        return new AccessTokenResource($user);
    }
}
