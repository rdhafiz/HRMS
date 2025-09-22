<?php

namespace App\Http\Controllers\Api;

use App\Constants\LeaveTypes;
use App\Constants\UserRoles;
use App\Http\Controllers\Controller;
use App\Mail\LeaveApplicationMail;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only allow employees to access this endpoint
        if ($user->admin_type !== UserRoles::EMPLOYEE) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Get employee record
        $employee = Employee::where('email', $user->email)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee profile not found'], 404);
        }

        $query = LeaveRequest::query()->where('employee_id', $employee->id);

        // Status filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filtering
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Leave type filtering
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // Order by created date (most recent first)
        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Add formatted data
        $leaveRequests->getCollection()->transform(function ($leaveRequest) {
            $leaveRequest->formatted_dates = $this->formatLeaveDates($leaveRequest);
            $leaveRequest->status_label = $this->getStatusLabel($leaveRequest->status);
            $leaveRequest->status_color = $this->getStatusColor($leaveRequest->status);
            $leaveRequest->leave_type_label = $this->getLeaveTypeLabel($leaveRequest->leave_type);
            
            return $leaveRequest;
        });

        return response()->json($leaveRequests);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only allow employees to access this endpoint
        if ($user->admin_type !== UserRoles::EMPLOYEE) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Get employee record
        $employee = Employee::where('email', $user->email)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee profile not found'], 404);
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'application_body' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'leave_type' => ['required', Rule::in(LeaveTypes::all())],
        ]);

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'subject' => $validated['subject'],
            'application_body' => $validated['application_body'],
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['application_body'], // Keep for backward compatibility
            'status' => 'pending',
        ]);

        // Log the action
        UserLog::create([
            'user_id' => $user->id,
            'type' => 'leave_application_create',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['leave_request_id' => $leaveRequest->id],
        ]);

        // Send email notification to HR Admin
        $this->sendLeaveApplicationEmail($leaveRequest);

        return response()->json([
            'message' => 'Leave application submitted successfully',
            'leave_request' => $leaveRequest->load('employee')
        ], 201);
    }

    private function processDates($dateType, $specificDates)
    {
        if (empty($specificDates)) {
            return null;
        }

        switch ($dateType) {
            case 'single':
                return [
                    'start_date' => $specificDates[0],
                    'end_date' => $specificDates[0]
                ];
            case 'range':
                if (count($specificDates) >= 2) {
                    return [
                        'start_date' => min($specificDates),
                        'end_date' => max($specificDates)
                    ];
                }
                break;
            case 'multiple':
                return [
                    'start_date' => min($specificDates),
                    'end_date' => max($specificDates)
                ];
        }

        return null;
    }

    private function formatLeaveDates($leaveRequest)
    {
        switch ($leaveRequest->date_type) {
            case 'single':
                return $leaveRequest->start_date->format('M d, Y');
            case 'range':
                return $leaveRequest->start_date->format('M d, Y') . ' - ' . $leaveRequest->end_date->format('M d, Y');
            case 'multiple':
                $dates = collect($leaveRequest->specific_dates)
                    ->map(function ($date) {
                        return \Carbon\Carbon::parse($date)->format('M d, Y');
                    })
                    ->join(', ');
                return $dates;
            default:
                return $leaveRequest->start_date->format('M d, Y');
        }
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
        ];

        return $colors[$status] ?? 'gray';
    }

    private function getLeaveTypeLabel($leaveType)
    {
        $labels = [
            'sick' => 'Sick Leave',
            'casual' => 'Casual Leave',
            'paid' => 'Paid Leave',
            'unpaid' => 'Unpaid Leave',
        ];

        return $labels[$leaveType] ?? ucfirst($leaveType);
    }

    private function sendLeaveApplicationEmail($leaveRequest)
    {
        try {
            // Get HR Admin users
            $hrAdmins = User::whereIn('admin_type', [UserRoles::SYSTEM_ADMIN, UserRoles::HR_MANAGER])->get();
            
            foreach ($hrAdmins as $hrAdmin) {
                Mail::to($hrAdmin->email)->send(new LeaveApplicationMail($leaveRequest));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send leave application email: ' . $e->getMessage());
        }
    }
}
