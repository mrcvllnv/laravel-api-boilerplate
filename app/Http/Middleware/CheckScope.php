<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckScope
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @param array  $scopes
     * @return mixed
     */
    public function handle($request, Closure $next, ... $scopes)
    {
        abort_if(! in_array(auth()->payload()->get('scope'), $scopes), 403);

        return $next($request);
    }
}
