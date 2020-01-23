<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

Route::post('login', LoginController::class)->name('login');
Route::post('logout', LogoutController::class)->name('logout');