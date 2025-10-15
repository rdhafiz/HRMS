<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    protected int $expiryMinutes = 30;

    public function sendResetCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->reset_code = $code;
        $user->reset_code_sent_at = now();
        $user->save();

        UserLog::create([
            'user_id' => $user->id,
            'type' => 'forgot',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['email' => $user->email],
        ]);

        // Send email with reset code
        try {
            Mail::raw("Your password reset code is: {$code}\n\nThis code will expire in {$this->expiryMinutes} minutes.", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset Code - HR Portal');
            });
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Reset code sent to your email',
            'dev_code' => app()->isLocal() ? $code : null
        ]);
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();
        
        if (!$user->reset_code || $user->reset_code !== $validated['code']) {
            return response()->json(['message' => 'Invalid code'], 422);
        }
        
        if (!$user->reset_code_sent_at || $user->reset_code_sent_at->addMinutes($this->expiryMinutes)->isPast()) {
            return response()->json(['message' => 'Code expired'], 422);
        }

        return response()->json(['message' => 'Code valid']);
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if (!$user->reset_code || $user->reset_code !== $validated['code']) {
            UserLog::create([
                'user_id' => $user->id,
                'type' => 'failed_reset',
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'meta' => ['reason' => 'invalid_code'],
            ]);
            return response()->json(['message' => 'Invalid code'], 422);
        }

        if (!$user->reset_code_sent_at || $user->reset_code_sent_at->addMinutes($this->expiryMinutes)->isPast()) {
            UserLog::create([
                'user_id' => $user->id,
                'type' => 'failed_reset',
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'meta' => ['reason' => 'code_expired'],
            ]);
            return response()->json(['message' => 'Code expired'], 422);
        }

        // Update password and clear reset code
        $user->password = Hash::make($validated['password']);
        $user->reset_code = null;
        $user->reset_code_sent_at = null;
        $user->save();

        // Revoke all existing tokens for security
        $user->tokens()->delete();

        UserLog::create([
            'user_id' => $user->id,
            'type' => 'reset',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => null,
        ]);

        return response()->json(['message' => 'Password reset successful']);
    }
}
