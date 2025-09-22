<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserLog;
use App\Constants\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->admin_type !== UserRoles::EMPLOYEE) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)
            ->with([
                'department',
                'designation',
                'currentSalaryStructure.structure.components'
            ])
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee profile not found'], 404);
        }

        return response()->json($employee);
    }

    public function activityLogs(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee profile not found'], 404);
        }

        $perPage = $request->query('per_page', 10);
        
        $logs = UserLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($logs);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee profile not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'phone' => ['nullable', 'string', 'max:191'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address' => ['nullable', 'string'],
        ]);

        // Update employee record
        $employee->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
        ]);

        // Update user record name
        User::where('id', $user->id)->update(['name' => $employee->name]);

        // Log the activity
        UserLog::create([
            'user_id' => $user->id,
            'type' => 'profile_update',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['employee_id' => $employee->id],
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'employee' => $employee->load(['department', 'designation'])
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The provided current password is incorrect.',
                'errors' => ['current_password' => ['The provided current password is incorrect.']]
            ], 422);
        }

        // Update password
        User::where('id', $user->id)->update(['password' => Hash::make($validated['password'])]);

        // Log the activity
        UserLog::create([
            'user_id' => $user->id,
            'type' => 'password_change',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => [],
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}
