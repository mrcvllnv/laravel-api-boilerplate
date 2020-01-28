<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshTokenHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function testRefreshTokenSuccessful()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->postJson(route('refresh'))
            ->assertOk()
            ->assertJsonStructure(['meta' => ['token' => ['access_token']]]);
    }

    public function testRefreshTokenFailed()
    {
        $this->postJson(route('refresh'))
            ->assertUnauthorized();
    }
}
