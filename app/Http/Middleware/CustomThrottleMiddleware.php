<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Symfony\Component\HttpFoundation\Response;

class CustomThrottleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limiter = 'api'): Response
    {
        // For authenticated users, be more lenient with rate limiting
        if ($request->user()) {
            // Check if user has exceeded a more generous limit
            $key = 'throttle:' . $limiter . ':' . $request->user()->id;
            $maxAttempts = $this->getMaxAttemptsForUser($limiter);
            $decayMinutes = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $retryAfter = RateLimiter::availableIn($key);
                $minutes = ceil($retryAfter / 60);
                
                throw new ThrottleRequestsException(
                    'Too many requests. Please wait ' . $minutes . ' minute(s) before trying again.',
                    null,
                    ['Retry-After' => $retryAfter]
                );
            }

            RateLimiter::hit($key, $decayMinutes * 60);
        } else {
            // For unauthenticated users, use standard throttling
            $key = 'throttle:' . $limiter . ':' . $request->ip();
            $maxAttempts = 60; // Standard rate limit for unauthenticated users
            $decayMinutes = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $retryAfter = RateLimiter::availableIn($key);
                $minutes = ceil($retryAfter / 60);
                
                throw new ThrottleRequestsException(
                    'Too many requests. Please wait ' . $minutes . ' minute(s) before trying again.',
                    null,
                    ['Retry-After' => $retryAfter]
                );
            }

            RateLimiter::hit($key, $decayMinutes * 60);
        }

        return $next($request);
    }

    /**
     * Get max attempts based on the limiter type and user authentication status.
     */
    private function getMaxAttemptsForUser(string $limiter): int
    {
        return match ($limiter) {
            'login' => 10, // More attempts for authenticated users
            'password-reset' => 5,
            'dashboard' => 2000, // Very generous for dashboard data
            'api' => 500, // More generous for general API calls
            default => 300,
        };
    }
}