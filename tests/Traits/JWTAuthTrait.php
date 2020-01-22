<?php

namespace Tests\Traits;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Contracts\Auth\Authenticatable;

trait JWTAuthTrait
{
    /**
     * @param Authenticatable $user
     * 
     * @return $this
     */
    public function auth(Authenticatable $user)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        if (auth()->check()) {
            auth()->logout();
        }

        return $this;
    }
}