<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->needsTwoFactor()) {
            return $next($request);
        }

        if ($request->routeIs('verify.*', 'auth.sessions.*', 'logout', 'login')) {
            return $next($request);
        }

        $lastActivity = session('last_activity_time');
        $timeoutMinutes = (int) config('auth.staff_timeout', 120);

        if ($lastActivity && (time() - $lastActivity) > ($timeoutMinutes * 60)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'نشست شما به دلیل عدم فعالیت منقضی شد.');
        }

        session(['last_activity_time' => time()]);

        return $next($request);
    }
}
