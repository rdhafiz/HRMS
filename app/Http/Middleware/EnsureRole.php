<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $allowedRoleIds = collect(explode('|', $roles))
            ->map(fn ($r) => trim($r))
            ->filter()
            ->map(function ($name) {
                return \App\Constants\UserRoles::fromString($name);
            })
            ->filter()
            ->values();

        $allowed = $allowedRoleIds->contains((int) $user->admin_type);

        if (!$allowed) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}

