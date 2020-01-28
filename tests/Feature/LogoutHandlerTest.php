<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserLogoutSuccess()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->postJson(route('logout'))
            ->assertOk()
            ->assertJsonFragment(['result' => true]);

        $this->postJson(route('logout'))
            ->assertUnauthorized();
    }

    public function testUserCannotLogoutIfNotCurrentlyLoggedIn()
    {
        $this->postJson(route('logout'))
            ->assertUnauthorized();
    }
}
