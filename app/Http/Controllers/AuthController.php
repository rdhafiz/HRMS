<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $credentials = ['email' => $validated['email'], 'password' => $validated['password']];

        if (!Auth::attempt($credentials, true)) {
            UserLog::create([
                'user_id' => optional(User::where('email', $validated['email'])->first())->id,
                'type' => 'failed_login',
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'meta' => ['email' => $validated['email'], 'reason' => 'invalid_credentials'],
            ]);

            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        UserLog::create([
            'user_id' => $user->id,
            'type' => 'login',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => null,
        ]);

        return response()->json($user);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        UserLog::create([
            'user_id' => optional($user)->id,
            'type' => 'logout',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => null,
        ]);

        return response()->json(['message' => 'Logged out']);
    }
}

