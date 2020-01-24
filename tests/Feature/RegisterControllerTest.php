<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\VerificationCodeNotification;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->attributes = factory(User::class)->make();
    }

    public function testGuestUserCanRegister()
    {
        Notification::fake();

        $this->postJson(route('register'), [
            'name' => $this->attributes->name,
            'email' => $this->attributes->email,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])
        ->assertStatus(201);

        $user = User::whereEmail($this->attributes->email)->firstOrFail();

        Notification::assertSentTo($user, VerificationCodeNotification::class,
            function (VerificationCodeNotification $notification) use ($user) {
                return Hash::check($notification->code, $user->verification_code);
            });
    }

    public function testGuestUserCannotRegisterWithoutEmail()
    {
        $this->postJson(route('register'), [
            'name' => $this->attributes->name,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['email' => [trans('validation.required', [ 'attribute' => 'email' ])]]);
    }

    public function testGuestUserCannotRegisterIfEmailIsAlreadyTaken()
    {
        $user = factory(User::class)->create([
            'email' => 'existing@email.com'
        ]);

        $this->postJson(route('register'), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['email' => [trans('validation.unique', [ 'attribute' => 'email' ])]]);
    }

    public function testGuestUserCannotRegisterWithoutPassword()
    {
        $this->postJson(route('register'), [
            'name' => $this->attributes->name,
            'email' => $this->attributes->email,
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['password' => [trans('validation.required', [ 'attribute' => 'password' ])]]);
    }

    public function testGuestUserCannotRegisterWithoutPasswordConfirmation()
    {
        $this->postJson(route('register'), [
            'name' => $this->attributes->name,
            'email' => $this->attributes->email,
            'password' => '12345678',
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['password' => [trans('validation.confirmed', [ 'attribute' => 'password' ])]]);
    }

    public function testGuestUserCannotRegisterWithInvalidPassword()
    {
        $this->postJson(route('register'), [
            'name' => $this->attributes->name,
            'email' => $this->attributes->email,
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['password' => [trans('validation.min.string', [ 'attribute' => 'password', 'min' => 8 ])]]);

        $this->postJson(route('register'), [
            'name' => $this->attributes->name,
            'email' => $this->attributes->email,
            'password' => 12345678,
            'password_confirmation' => 12345678,
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['password' => [trans('validation.string', [ 'attribute' => 'password' ])]]);
    }

    public function testGuestUserCannotRegisterWithIncorrectPasswordConfirmation()
    {
        $this->postJson(route('register'), [
            'name' => $this->attributes->name,
            'email' => $this->attributes->email,
            'password' => '12345678',
            'password_confirmation' => '1234567',
        ])
        ->assertStatus(422)
        ->assertJsonFragment(['password' => [trans('validation.confirmed', [ 'attribute' => 'password' ])]]);
    }
}
