<?php

namespace App\Http\Controllers\Auth;

use App\Constants\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MicrosoftAuthController extends Controller
{
    public function redirectToMicrosoft()
    {
        $clientId = config('services.microsoft.client_id');
        $redirectUri = config('services.microsoft.redirect');
        $tenant     = config('services.microsoft.tenant', 'common');
        $state = Str::random(40);

        session(['microsoft_oauth_state' => $state]);

        $scope = implode(' ', [
            'openid',
            'profile',
            'email',
            'offline_access',
            'https://graph.microsoft.com/User.Read',
        ]);

        $params = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state,
            'response_mode' => 'query'
        ]);

        return redirect("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/authorize?{$params}");
    }

    public function handleMicrosoftCallback(Request $request)
    {
        try {
            // Verify state parameter
            if (!$request->has('state') || $request->state !== session('microsoft_oauth_state')) {
                return redirect('/')->with('error', 'Invalid state parameter.');
            }
            session()->forget('microsoft_oauth_state');

            // Exchange code for token - Fix the URL interpolation
            $tenant = config('services.microsoft.tenant', 'common');
            $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token", [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'code' => $request->input('code'),
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.microsoft.redirect'),
            ]);

            if (!$tokenResponse->successful()) {
                Log::error('Microsoft token exchange failed', [
                    'response' => $tokenResponse->json(),
                    'status' => $tokenResponse->status()
                ]);
                throw new \Exception('Failed to get access token: ' . $tokenResponse->body());
            }

            $accessToken = $tokenResponse->json('access_token');

            // Get user info from Microsoft Graph
            $userResponse = Http::withToken($accessToken)->get('https://graph.microsoft.com/v1.0/me');

            if (!$userResponse->successful()) {
                Log::error('Microsoft user info failed', [
                    'response' => $userResponse->json(),
                    'status' => $userResponse->status()
                ]);
                throw new \Exception('Failed to get user information: ' . $userResponse->body());
            }

            $microsoftUser = $userResponse->json();

            // Check if user already exists
            $user = User::where('email', $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'])->first();

            if ($user) {
                if ($user->account_source !== 'microsoft_login') {
                    return redirect('/')
                        ->with('error', 'This email is already registered as an admin account. Please use the regular login.');
                }
            } else {
                // Create new user and employee
                return $this->createMicrosoftUser($microsoftUser, $request);
            }

            Auth::login($user);

            UserLog::create([
                                'user_id' => $user->id,
                                'type' => 'microsoft_login',
                                'ip' => $request->ip(),
                                'user_agent' => (string) $request->userAgent(),
                                'meta' => ['microsoft_id' => $microsoftUser['id'] ?? null],
                            ]);

            return redirect()->intended('/employee/dashboard');
        } catch (\Exception $e) {
            Log::error('Microsoft OAuth Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Microsoft login failed. Please try again.');
        }
    }

    private function createMicrosoftUser($microsoftUser, Request $request)
    {
        DB::beginTransaction();

        try {
            // Get default department and designation (or create them if they don't exist)
            $defaultDepartment = Department::first();
            $defaultDesignation = Designation::first();

            if (!$defaultDepartment) {
                $defaultDepartment = Department::create([
                    'name' => 'General',
                    'description' => 'Default department for Microsoft login users',
                ]);
            }

            if (!$defaultDesignation) {
                $defaultDesignation = Designation::create([
                    'name' => 'Employee',
                    'description' => 'Default designation for Microsoft login users',
                ]);
            }

            // Create user
            $user = User::create([
                'name' => $microsoftUser['displayName'] ?? 'Microsoft User',
                'email' => $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'],
                'email_verified_at' => now(), // Auto-verify Microsoft users
                'password' => bcrypt(Str::random(32)), // Random password since they'll use Microsoft
                'admin_type' => UserRoles::EMPLOYEE,
                'account_source' => 'microsoft_login',
                'microsoft_id' => $microsoftUser['id'],
            ]);

            // Generate employee code
            $employeeCode = 'EMP' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create employee record linked to user
            $employee = Employee::create([
                'user_id' => $user->id, // Link employee to user
                'first_name' => $this->extractFirstName($microsoftUser['displayName'] ?? 'Microsoft User'),
                'last_name' => $this->extractLastName($microsoftUser['displayName'] ?? 'Microsoft User'),
                'email' => $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'],
                'employee_code' => $employeeCode,
                'join_date' => now()->toDateString(),
                'department_id' => $defaultDepartment->id,
                'designation_id' => $defaultDesignation->id,
                'status' => 'active',
                'account_source' => 'microsoft_login',
                'microsoft_id' => $microsoftUser['id'],
            ]);

            // Log the account creation
            UserLog::create([
                'user_id' => $user->id,
                'type' => 'microsoft_account_created',
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'meta' => [
                    'microsoft_id' => $microsoftUser['id'],
                    'employee_id' => $employee->id,
                ],
            ]);

            DB::commit();

            // Login the user
            Auth::login($user);

            // For SPA, return JSON response or redirect to frontend
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Welcome! Your account has been created successfully.',
                    'user' => $user,
                    'employee' => $employee,
                    'redirect' => '/employee/dashboard'
                ]);
            }

            return redirect('/employee/dashboard')->with('success', 'Welcome! Your account has been created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Microsoft User Creation Error: ' . $e->getMessage(), [
                'microsoft_user' => $microsoftUser,
                'error' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create account. Please try again.'
                ], 500);
            }

            return redirect('/')->with('error', 'Failed to create account. Please try again.');
        }
    }

    private function extractFirstName($fullName)
    {
        $nameParts = explode(' ', trim($fullName));
        return $nameParts[0] ?? 'Unknown';
    }

    private function extractLastName($fullName)
    {
        $nameParts = explode(' ', trim($fullName));
        return count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'User';
    }
}
