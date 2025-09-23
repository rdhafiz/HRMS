<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle rate limiting exceptions with friendly messages
        if ($e instanceof ThrottleRequestsException) {
            return $this->handleThrottleException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle throttle requests exception with friendly messages.
     */
    protected function handleThrottleException(Request $request, ThrottleRequestsException $e): JsonResponse
    {
        $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
        $minutes = ceil($retryAfter / 60);

        // Determine the type of rate limiting based on the route
        $message = 'Too many requests. Please try again later.';
        
        if ($request->is('api/auth/login')) {
            $message = 'Too many login attempts. Please wait ' . $minutes . ' minute(s) before trying again.';
        } elseif ($request->is('api/auth/forgot') || $request->is('api/auth/verify-reset-code') || $request->is('api/auth/reset')) {
            $message = 'Too many password reset attempts. Please wait ' . $minutes . ' minute(s) before trying again.';
        } elseif ($request->is('api/dashboard/*')) {
            $message = 'Dashboard data requests are being rate limited. Please wait ' . $minutes . ' minute(s) before refreshing.';
        }

        return response()->json([
            'message' => $message,
            'error' => 'Too Many Requests',
            'retry_after' => $retryAfter,
            'retry_after_minutes' => $minutes
        ], 429);
    }
}
