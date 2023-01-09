<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return $next($request);
        } elseif ($_SERVER['REQUEST_URI'] == '/users/' . Auth::user()->id) {
            return $next($request);
        } elseif (str_starts_with($_SERVER['REQUEST_URI'], '/api/')) {
            return $next($request);
        }
        return back()->withInput();;
    }
}
