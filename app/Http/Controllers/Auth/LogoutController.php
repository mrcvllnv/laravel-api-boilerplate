<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
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
     * Logout the user
     *
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        auth()->logout();

        return response()->json(['result' => true]);
    }
}
