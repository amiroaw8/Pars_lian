<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isEmployee()) {
            return redirect()
                ->route('auth.dashboard')
                ->with('info', 'حساب کارمندی شما به پنل مدیریت متصل است. از آنجا ادامه دهید.');
        }

        return $next($request);
    }
}
