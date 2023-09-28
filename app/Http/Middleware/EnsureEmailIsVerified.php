<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $redirectToRoute
     * @return JsonResponse|RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $user = $request->user();
        if (!$user || ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail())) {
            abort(409, 'Ваш email не подтвержден');
        }
        return $next($request);
    }
}
