<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class SecurityAudit extends Command
{
    protected $signature = 'app:security-audit';
    protected $description = 'Perform a security audit on the application';

    private $sensitiveFields = [
        'password', 'remember_token', 'status', 'role', 'is_admin', 
        'service_cost', 'amount', 'balance', 'permissions'
    ];

    private $publicRoutes = [
        '/', 'login', 'register', 'up', 'tracking', 'catalog*', 'product*', 
        'shop*', 'cart*', 'api/v1/products*', 'api/device-types*'
    ];

    public function handle()
    {
        $this->header('Application Security Audit');

        $this->auditEnvironment();
        $this->auditMassAssignment();
        $this->auditRoutes();
        $this->auditMiddleware();

        $this->info("\nAudit complete.");
    }

    private function header($text)
    {
        $this->line("\n<options=bold;bg=blue;fg=white> $text </>\n");
    }

    private function auditEnvironment()
    {
        $this->info("Checking Environment Settings...");
        
        $debug = config('app.debug');
        $env = config('app.env');

        if ($env === 'production') {
            if ($debug) {
                $this->error("❌ CRITICAL: APP_DEBUG is enabled in production!");
            } else {
                $this->line("✅ APP_DEBUG is disabled (Production).");
            }
            $this->line("✅ APP_ENV is set to production.");
        } else {
            if ($debug) {
                $this->line("ℹ APP_DEBUG is enabled (Development mode).");
            } else {
                $this->line("✅ APP_DEBUG is disabled.");
            }
            $this->line("ℹ APP_ENV is set to '$env'.");
        }
    }

    private function auditMassAssignment()
    {
        $this->info("\nChecking Models for Mass Assignment Vulnerabilities...");
        
        $modelPath = app_path('Models');
        $files = File::allFiles($modelPath);
        $foundIssues = 0;

        foreach ($files as $file) {
            $class = 'App\\Models\\' . $file->getBasename('.php');
            if (!class_exists($class)) continue;

            $reflection = new ReflectionClass($class);
            if ($reflection->isAbstract()) continue;

            $model = new $class;
            if (method_exists($model, 'getFillable')) {
                $fillable = $model->getFillable();
                $issues = array_intersect($this->sensitiveFields, $fillable);

                if (!empty($issues)) {
                    $this->warn("⚠ Model '$class' has sensitive fields in \$fillable: " . implode(', ', $issues));
                    $foundIssues++;
                }
            }
        }

        if ($foundIssues === 0) {
            $this->line("✅ No sensitive fields found in Model \$fillable arrays.");
        }
    }

    private function auditRoutes()
    {
        $this->info("\nChecking Routes for Security...");
        
        $routes = Route::getRoutes();
        $unprotectedRoutes = [];
        $unthrottledRoutes = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            
            // Skip internal Laravel routes
            if (str_starts_with($uri, '_') || str_starts_with($uri, 'sanctum')) continue;

            $middleware = $route->gatherMiddleware();

            // Check for authentication
            $hasAuth = false;
            foreach ($middleware as $m) {
                if (str_contains($m, 'auth') || str_contains($m, 'guest')) {
                    $hasAuth = true;
                    break;
                }
            }

            if (!$hasAuth) {
                $isPublic = false;
                foreach ($this->publicRoutes as $pattern) {
                    if (fnmatch($pattern, $uri)) {
                        $isPublic = true;
                        break;
                    }
                }

                if (!$isPublic) {
                    $unprotectedRoutes[] = $uri;
                }
            }

            // Check for rate limiting
            $hasThrottle = false;
            foreach ($middleware as $m) {
                if (str_contains($m, 'throttle') || str_contains($m, 'ratelimit')) {
                    $hasThrottle = true;
                    break;
                }
            }

            if (!$hasThrottle && (str_contains($uri, 'api') || str_contains($uri, 'login') || str_contains($uri, 'register'))) {
                $unthrottledRoutes[] = $uri;
            }
        }

        if (!empty($unprotectedRoutes)) {
            $this->warn("⚠ Found " . count($unprotectedRoutes) . " routes without explicit authentication middleware.");
            if ($this->confirm('Show unprotected routes?')) {
                $this->table(['URI'], array_map(fn($u) => [$u], $unprotectedRoutes));
            }
        } else {
            $this->line("✅ All sensitive routes appear to be protected by authentication.");
        }

        if (!empty($unthrottledRoutes)) {
            $this->warn("⚠ Found " . count($unthrottledRoutes) . " API/Auth routes without rate limiting.");
            if ($this->confirm('Show unthrottled routes?')) {
                $this->table(['URI'], array_map(fn($u) => [$u], $unthrottledRoutes));
            }
        } else {
            $this->line("✅ API and Auth routes have rate limiting applied.");
        }
    }

    private function auditMiddleware()
    {
        $this->info("\nChecking Global Middleware...");
        
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $reflection = new ReflectionClass($kernel);
        
        // This is a bit tricky to check global middleware dynamically, 
        // but we can check for specific classes in the kernel property if we can access it.
        // For now, we'll check common ones.
        
        $hasSecurityHeaders = class_exists(\App\Http\Middleware\SecurityHeaders::class);
        $hasSanitization = class_exists(\App\Http\Middleware\SanitizeInput::class);

        if ($hasSecurityHeaders) {
            $this->line("✅ SecurityHeaders middleware is present.");
        } else {
            $this->warn("⚠ SecurityHeaders middleware is missing.");
        }

        if ($hasSanitization) {
            $this->line("✅ SanitizeInput middleware is present.");
        } else {
            $this->warn("⚠ SanitizeInput middleware is missing.");
        }
    }
}
