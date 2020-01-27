<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;

Route::post('login', LoginController::class)->name('login');
Route::post('logout', LogoutController::class)->name('logout');
Route::post('register', RegisterController::class)->name('register');
Route::post('email/verify', VerificationController::class.'@verify')->name('verification.verify');
Route::post('email/resend', VerificationController::class.'@resend')->name('verification.resend');
Route::post('password/email', ForgotPasswordController::class)->name('password.email');
Route::post('password/reset', ResetPasswordController::class)->name('password.update');
