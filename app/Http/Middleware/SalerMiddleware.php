<?php

namespace App\Http\Middleware;

use App\Models\Traits\HasVerify;
use Closure;
use Illuminate\Http\Request;

class SalerMiddleware
{

    use HasVerify;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $role = session('user.role');
        $this->verify($role);
        if ($role == 'saler' || $role == 'admin') return $next($request);
        abort(403);
    }
}
