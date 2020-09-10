<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get user by email address
     *
     * @param  string  $email
     * @return \App\Models\User
     */
    public function getByEmail(string $email): User;

    /**
     * Send the email verification notification.
     *
     * @param  \App\Models\User  $user
     * @return boolean
     */
    public function sendVerificationCode(User $user): bool;

    /**
     * Handle a registration request for the application.
     *
     * @param  array  $attributes
     * @return \App\Models\User
     */
    public function register(array $attributes): User;

    /**
     * Handle a login request to the application.
     *
     * @param  string  $email
     * @param  string  $password
     * @return \App\Models\User
     */
    public function login(string $email, string $password): User;

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \App\Models\User  $user
     * @param  integer  $code
     * @return boolean
     */
    public function verifyEmail(User $user, int $code): bool;

    /**
     * Resend the email verification code notification.
     *
     * @param \App\Models\User $user
     * @return boolean
     */
    public function resendVerificationCode(User $user): bool;
    
    /**
     * Send a password reset link to a user.
     *
     * @param  string  $email
     * @return boolean
     */
    public function sendResetLink(string $email): bool;

    /**
     * Reset the password for the given token.
     *
     * @param  string  $token
     * @param  string  $password
     * @return boolean
     */
    public function resetPassword(string $token, string $password): bool;
}