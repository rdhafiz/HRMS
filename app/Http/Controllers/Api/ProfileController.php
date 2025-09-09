<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $lastLogin = UserLog::where('user_id', $user->id)
            ->where('type', 'login')
            ->latest('created_at')
            ->first();

        return response()->json([
            'user' => $user,
            'last_login_at' => optional($lastLogin)->created_at,
        ]);
    }

    public function logs(Request $request)
    {
        $user = $request->user();
        $perPage = (int) $request->get('per_page', 50);
        if ($perPage <= 0 || $perPage > 200) {
            $perPage = 50;
        }

        $logs = UserLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($logs);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->name = $validated['name'];

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->getOriginal('avatar');
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            if ($oldAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }
        }

        $user->save();

        return response()->json($user->fresh());
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                PasswordRule::min(8)->letters()->numbers()->symbols(),
                'confirmed',
            ],
            'logout' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        $user->password = $validated['new_password'];
        $user->save();

        UserLog::create([
            'user_id' => $user->id,
            'type' => 'password_change',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => null,
        ]);

        if ($request->boolean('logout', false)) {
            // Log out current session and require re-login
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            UserLog::create([
                'user_id' => $user->id,
                'type' => 'logout',
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'meta' => ['reason' => 'password_change'],
            ]);

            return response()->json(['message' => 'Password updated. Please log in again.']);
        }

        return response()->json(['message' => 'Password updated successfully.']);
    }
}


