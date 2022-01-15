<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GlobalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('id')) {
            abort(403,"You can not access this page as you are already logged in!!");
        }
        return $next($request);
    }
}
