<?php

use App\Http\Controllers\Auth\ForgotPasswordHandler;
use App\Http\Controllers\Auth\LoginHandler;
use App\Http\Controllers\Auth\LogoutHandler;
use App\Http\Controllers\Auth\RefreshTokenHandler;
use App\Http\Controllers\Auth\RegistrationHandler;
use App\Http\Controllers\Auth\ResetPasswordHandler;
use App\Http\Controllers\Auth\VerificationHandler;

Route::post('login', LoginHandler::class)->name('login');
Route::post('logout', LogoutHandler::class)->name('logout');
Route::post('register', RegistrationHandler::class)->name('register');
Route::post('email/verify', VerificationHandler::class.'@verify')->name('verification.verify');
Route::post('email/resend', VerificationHandler::class.'@resend')->name('verification.resend');
Route::post('password/email', ForgotPasswordHandler::class)->name('password.email');
Route::post('password/reset', ResetPasswordHandler::class)->name('password.update');
Route::post('refresh', RefreshTokenHandler::class)->name('refresh');
