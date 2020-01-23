<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;

Route::post('login', LoginController::class)->name('login');
Route::post('logout', LogoutController::class)->name('logout');
Route::post('register', RegisterController::class)->name('register');