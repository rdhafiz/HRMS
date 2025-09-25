<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecureMicrosoftAuth
{
    /**
     * Handle an incoming request for Microsoft OAuth security.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log all Microsoft OAuth attempts for security monitoring
        Log::info('Microsoft OAuth attempt', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => $request->route()->getName(),
            'parameters' => $request->all()
        ]);

        // Validate Microsoft callback parameters
        if ($request->routeIs('*.microsoft.callback')) {
            if ($request->has('error')) {
                Log::warning('Microsoft OAuth error callback', [
                    'error' => $request->input('error'),
                    'error_description' => $request->input('error_description'),
                    'ip' => $request->ip()
                ]);

                return redirect('/')->with('error', 'Microsoft authentication was cancelled or failed.');
            }

            if (!$request->has(['code', 'state'])) {
                Log::warning('Microsoft OAuth callback missing required parameters', [
                    'ip' => $request->ip(),
                    'has_code' => $request->has('code'),
                    'has_state' => $request->has('state')
                ]);

                return redirect('/')->with('error', 'Invalid Microsoft authentication response.');
            }
        }

        return $next($request);
    }
}
