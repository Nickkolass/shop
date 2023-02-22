<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientMiddleware
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
        if (auth()->check()) {
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            $user = auth()->user();
            if ($user->role == 'admin') {
                return $next($request);
            } elseif (($uri['1'] == 'ordering') || ($uri['2'] == $user->id)) {
                return $next($request);
            }
        }
        abort(404);
    }
}
