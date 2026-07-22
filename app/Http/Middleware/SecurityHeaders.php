<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /** Handle an incoming request. */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://api.zarinpal.com https://api.sms.ir https://api.telegram.org https://flareapp.io https://pusher.com; style-src 'self' 'unsafe-inline'; font-src 'self'; img-src 'self' data: https://images.unsplash.com https://via.placeholder.com https://picsum.photos https:; connect-src 'self' https://api.zarinpal.com https://api.sms.ir https://api.telegram.org https://flareapp.io https://pusher.com https://www.google.com; frame-src 'self' https://www.zarinpal.com https://sandbox.zarinpal.com https://www.google.com;");
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        $requestPath = $request->getPathInfo();
        $isStaticAsset = preg_match('/\.(css|js|woff2|woff|ttf|eot|svg|png|jpg|jpeg|webp|avif|ico)$/i', $requestPath) === 1
            || str_contains($requestPath, '/build/')
            || str_contains($requestPath, '/fonts/')
            || str_contains($requestPath, '/vendor/')
            || str_contains($requestPath, '/images/');

        if ($isStaticAsset) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            $response->headers->set('Cache-Control', 'no-store, no-cache, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
