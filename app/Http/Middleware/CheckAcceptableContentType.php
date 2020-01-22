<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use App\Exceptions\NotAcceptableException;
use App\Exceptions\UnsupportedMediaTypeException;

class CheckAcceptableContentType
{
    protected $exceptUrls = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Str::contains($request->url(), $this->exceptUrls)) {
            if ($request->header('Accept') != 'application/json') {
                throw new NotAcceptableException('Accepts only json format');
            }

            if (! $request->isMethod('get') && $request->header('Content-Type') != 'application/json') {
                throw new UnsupportedMediaTypeException();
            }
        }

        return $next($request);
    }
}
