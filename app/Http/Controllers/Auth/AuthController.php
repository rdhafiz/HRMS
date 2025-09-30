<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            UserLog::create([
                'user_id' => $user?->id,
                'type' => 'failed_login',
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'meta' => ['email' => $validated['email'], 'reason' => 'invalid_credentials'],
            ]);

            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        // Revoke all existing tokens for this user
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        UserLog::create([
            'user_id' => $user->id,
            'type' => 'login',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => null,
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        UserLog::create([
            'user_id' => $user->id,
            'type' => 'logout',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => null,
        ]);

        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
