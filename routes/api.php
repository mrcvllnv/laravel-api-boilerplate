<?php

use App\Http\Controllers\Auth\LoginController;

Route::post('login', LoginController::class)->name('login')->middleware('guest');