<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccessTokenResource;
use Illuminate\Http\Request;

class RefreshTokenHandler extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Handle the refresh token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\AccessTokenResource
     */
    public function __invoke(Request $request): AccessTokenResource
    {
        return new AccessTokenResource($request->user());
    }
}
