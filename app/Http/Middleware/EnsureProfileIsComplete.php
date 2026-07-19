<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow access to profile, logout, and checkout routes
        if ($request->routeIs('customer.profile') || 
            $request->routeIs('customer.profile.update') || 
            $request->routeIs('logout') ||
            $request->routeIs('checkout.*')) {
            return $next($request);
        }

        if ($user && (!$user->province || !$user->city || !$user->street || !$user->plate)) {
            $redirect = redirect()->route('customer.profile')
                ->with('warning', 'لطفاً ابتدا اطلاعات پروفایل خود را تکمیل کنید تا بتوانید از خدمات پنل استفاده کنید.');
            
            if (session('new_registration')) {
                $redirect->with('new_registration', true);
            }
            
            return $redirect;
        }

        return $next($request);
    }
}
