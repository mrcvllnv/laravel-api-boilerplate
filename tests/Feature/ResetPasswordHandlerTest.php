<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function testResetPasswordSuccessful()
    {
        $user = User::factory()->create();

        $token = Hash::make(Str::random(40));

        $result = DB::table(config('auth.passwords.users.table'))->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => now(),
        ]);

        $this->assertTrue($result);

        $this->assertDatabaseHas(config('auth.passwords.users.table'), [
            'email' => $user->email,
            'token' => $token,
        ]);

        $newPassword = 'newpassword';

        $this->postJson(route('password.update'), [
            'reset_token'           => $token,
            'password'              => $newPassword,
            'password_confirmation' => $newPassword,
        ])
        ->assertOk()
        ->assertJsonFragment(['result' => true]);

        $this->assertDatabaseMissing(config('auth.passwords.users.table'), [
            'email' => $user->email,
            'token' => $token,
        ]);
    }

    public function testResetPasswordWithoutPasswordConfirmation()
    {
        $this->postJson(route('password.update'), [
            'reset_token' => 'reset_token',
            'password'    => 'newpassword',
        ])
        ->assertStatus(422)
        ->assertJsonStructure(['error' => ['message' => ['password']]]);
    }

    public function testResetPasswordWithoutResetToken()
    {
        $newPassword = 'newpassword';

        $this->postJson(route('password.update'), [
            'password'              => $newPassword,
            'password_confirmation' => $newPassword,
        ])
        ->assertStatus(422)
        ->assertJsonStructure(['error' => ['message' => ['reset_token']]]);
    }
}
