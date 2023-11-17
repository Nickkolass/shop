<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @param int $role
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, int $role): Response|RedirectResponse
    {
        if (session()->exists('user.role') && session('user.role') <= $role) {
            return $next($request);
        }
        abort(404);
    }
}
