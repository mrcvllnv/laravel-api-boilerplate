<?php

namespace Tests\Feature;

use App\Notifications\VerificationCodeNotification;
use App\Notifications\VerifiedEmailNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerificationHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanVerifyEmail()
    {
        Notification::fake();

        $code = mt_rand(1000, 9999);

        $user = factory(User::class)->create([
            'email_verified_at' => null,
            'verification_code' => $code
        ]);

        $this->actingAs($user)
            ->postJson(route('verification.verify'), [
                'code' => $code
            ])
            ->assertOk()
            ->assertJsonFragment(['result' => true]);

        $this->assertNotNull($user->fresh()->email_verified_at);

        Notification::assertSentTo($user, VerifiedEmailNotification::class,
            function (VerifiedEmailNotification $notification) use ($user) {
                return $notification->user = $user;
            });
    }

    public function testUserEmailAlreadyVerified()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->postJson(route('verification.verify'), [
                'code' => 1234
            ])
            ->assertStatus(400);
    }

    public function testUserCannotVerifyEmailIfNotCurrentlyLoggedIn()
    {
        $this->postJson(route('verification.verify'), [
                'code' => 1234
            ])
            ->assertUnauthorized();
    }

    public function testUserCannotVerifyEmailWithIncorrectVerificationCode()
    {
        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->postJson(route('verification.verify'), [
                'code' => 1234
            ])
            ->assertStatus(400);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function testUserCannotVerifyEmailWithInvalidVerificationCode()
    {
        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->postJson(route('verification.verify'), [
                'code' => '1234'
            ])
            ->assertStatus(400);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function testUserCannotVerifyEmailWithExpiredVerificationCode()
    {
        $code = mt_rand(1000, 9999);

        $user = factory(User::class)->create([
            'email_verified_at' => null,
            'verification_code' => $code,
            'verification_code_expires_at' => now(),
        ]);

        Carbon::setTestNow(now()->addMinutes(60));

        $this->actingAs($user)
            ->postJson(route('verification.verify'), [
                'code' => $code
            ])
            ->assertStatus(400);
    }

    public function testUserCannotVerifyEmailWithoutVerificationCode()
    {
        $code = mt_rand(1000, 9999);

        $user = factory(User::class)->create([
            'email_verified_at' => null,
            'verification_code' => $code,
            'verification_code_expires_at' => now(),
        ]);

        Carbon::setTestNow(now()->addMinutes(60));

        $this->actingAs($user)
            ->postJson(route('verification.verify'))
            ->assertStatus(422)
            ->assertJsonFragment(['code' => [trans('validation.required', ['attribute' => 'code'])]]);
    }

    public function testUserVerifyEmailThrottle()
    {
        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        for ($i=0; $i < 3; $i++) { 
            $this->actingAs($user)
                ->postJson(route('verification.verify'), [
                    'code' => 1234
                ]);
        }

        $this->actingAs($user)
            ->postJson(route('verification.verify'), [
                'code' => 1234
            ])
            ->assertStatus(429);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function testUserCanResendVerificationCodeNotification()
    {
        Notification::fake();

        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->postJson(route('verification.resend'))
            ->assertOk()
            ->assertJsonFragment(['result' => true]);

        $this->assertNotNull($user->fresh()->verification_code_expires_at);

        Notification::assertSentTo($user, VerificationCodeNotification::class,
            function (VerificationCodeNotification $notification) use ($user) {
                return $notification->user = $user;
            });
    }

    public function testUserCannotResendVerificationCodeNotificationIfNotCurrentlyLoggedIn()
    {
        Notification::fake();

        $this->postJson(route('verification.resend'))
            ->assertUnauthorized();
    }

    public function testUserVerifyEmailResendThrottle()
    {
        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        for ($i=0; $i < 3; $i++) { 
            $this->actingAs($user)
                ->postJson(route('verification.resend'));
        }

        $this->actingAs($user)
            ->postJson(route('verification.resend'))
            ->assertStatus(429);
    }
}
