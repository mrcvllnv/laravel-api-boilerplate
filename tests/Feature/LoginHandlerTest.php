<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => '12345'
        ]);
    }
    

    public function testUserCanLogin()
    {
        $this->withoutExceptionHandling();

        $this->postJson(route('login'), [
            'email' => $this->user->email,
            'password' => '12345'
        ])
        ->assertOk()
        ->assertJsonStructure(['meta' => ['token' => ['access_token']]]);
    }

    public function testUserIsAlreadyLoggedIn()
    {
        $this->actingAs($this->user)
            ->postJson(route('login'))
            ->assertForbidden();
    }

    public function testUserEmailMustBeValidEmail()
    {
        $this->postJson(route('login'), [
            'email' => 'mm.com'
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['email' => [trans('validation.email', [ 'attribute' => 'email' ])]]);
    }
    
    public function testUserCannotLoginWithoutEmail()
    {
        $this->postJson(route('login'), [
            'password' => '12345'
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['email' => [trans('validation.required', [ 'attribute' => 'email' ])]]);
    }
    
    public function testUserCannotLoginWithoutPassword()
    {
        $this->postJson(route('login'), [
            'email' => $this->user->email
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['password' => [trans('validation.required', [ 'attribute' => 'password' ])]]);
    }

    public function testUserCannotLoginWithInvalidCredentials()
    {
        $response = $this->postJson(route('login'), [
            'email' => $this->user->email,
            'password' => 'wr0ngp4ssw0rd'
        ])
        ->assertUnauthorized()
        ->assertJsonFragment(['message' => trans('auth.failed')]);
    }
}
