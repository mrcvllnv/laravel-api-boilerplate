<?php

namespace Tests;

use Tests\Traits\JWTAuthTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, JWTAuthTrait;

    public function actingAs(Authenticatable $user, $driver = null): TestCase
    {
        return $this->auth($user);
    }
}
