<?php

namespace App\Http\Middleware;

use App\Models\Traits\HasVerify;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleMiddleware
{

    use HasVerify;

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
        $user_role = $this->verify();
        if (is_null($user_role) || $user_role > $role) abort(404);
        return $next($request);
    }
}
