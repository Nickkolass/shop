<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        dd('поправить clientMiddleware');
        $role = auth()->user()->role;
        if ($role == 'admin') {
            return $next($request);
        } elseif ($request->REQUEST_URI == '/users/'.Auth::user()->id) {
            return $next($request);
        } {
            return back()->withInput();;
        }
    }
}

