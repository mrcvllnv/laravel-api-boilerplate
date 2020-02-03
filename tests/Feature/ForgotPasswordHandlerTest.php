<?php

namespace Tests\Feature;

use App\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordHandlerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testForgotPasswordSuccessful()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $this->postJson(route('password.email'), [
                'email' => $user->email
            ])
            ->assertOk()
            ->assertJsonFragment(['result' => true]);

        $domain = trim(config('auth.passwords.reset_domain'), '/');
        $path = config('auth.passwords.reset_path');
        $token = DB::table(config('auth.passwords.users.table'))->whereEmail($user->email)->first()->token;

        $resetUrl = "$domain/$path/$token";

        Notification::assertSentTo($user, ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use ($resetUrl) {
                return $notification->resetUrl === $resetUrl;
            });
    }

    public function testForgotPasswordNonExistenceEmail()
    {
        $this->postJson(route('password.email'), [
                'email' => $this->faker->safeEmail
            ])
            ->assertNotFound();
    }
}
