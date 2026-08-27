<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$item, $key) {
            if (is_string($item)) {
                // Don't sanitize passwords or fields ending in _html or _content
                if (!in_array($key, ['password', 'password_confirmation']) && !str_ends_with($key, '_html') && !str_ends_with($key, '_content')) {
                    $item = strip_tags($item);
                    $item = trim($item);
                }
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
