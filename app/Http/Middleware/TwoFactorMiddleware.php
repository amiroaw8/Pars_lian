<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->needsTwoFactor()) {
            return $next($request);
        }

        if ($request->routeIs(
            'verify.*',
            'auth.sessions.*',
            'logout',
            'home',
            'shop.*',
            'catalog.*',
            'cart.*',
            'tracking.*'
        )) {
            return $next($request);
        }

        if (!session()->has('two_factor_verified')) {
            return redirect()->route('verify.index');
        }

        return $next($request);
    }
}
