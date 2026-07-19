<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TechnicianMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // بررسی آیا کاربر نقش تعمیرکار دارد
        $user = Auth::user();

        // استفاده از نقش کاربر برای بررسی دسترسی
        if (! $user->isTechnician()) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt to technician-only route', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames()->toArray(),
                'user_email' => $user->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'route' => request()->route() ? request()->route()->getName() : 'unknown',
            ]);
            abort(403, 'دسترسی فقط برای تعمیرکاران مجاز است');
        }

        return $next($request);
    }
}
