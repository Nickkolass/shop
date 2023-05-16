<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SalerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $role = session('user_role');
        if ($role == 'admin' || $role == 'saler') {
            return $next($request);
        }
        abort(404);
    }
}
