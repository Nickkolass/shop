<?php

namespace App\Http\Middleware;

use App\Models\Traits\HasVerify;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalerMiddleware
{

    use HasVerify;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $role = session('user.role');
        $this->verify($role);
        if ($role == 'saler' || $role == 'admin') return $next($request);
        abort(404);
    }
}
