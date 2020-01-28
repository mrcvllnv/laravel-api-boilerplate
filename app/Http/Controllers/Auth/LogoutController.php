<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\BooleanResource;

final class LogoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Log the user out of the application.
     *
     * @return \App\Http\Resources\BooleanResource
     */
    public function __invoke(): BooleanResource
    {
        auth()->logout();

        return new BooleanResource(true);
    }
}
