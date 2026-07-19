<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\SessionManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionLimit
{
    public function __construct(
        private readonly SessionManager $sessions
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !$this->sessions->usesDatabaseDriver()) {
            return $next($request);
        }

        if ($request->routeIs(
            'auth.sessions.*',
            'logout',
            'verify.*',
            'login',
            'register'
        )) {
            return $next($request);
        }

        if ($this->sessions->exceedsLimit((int) Auth::id())) {
            return Redirect::route('auth.sessions.limit');
        }

        return $next($request);
    }
}
