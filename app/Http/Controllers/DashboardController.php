<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use App\Models\Department;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get basic statistics
        $totalEmployees = Employee::count();
        
        // Today's attendance
        $today = now()->toDateString();
        $todayAttendance = Attendance::whereDate('date', $today)->get();
        
        $attendanceStats = [
            'present' => $todayAttendance->where('status', 'present')->count(),
            'absent' => $todayAttendance->where('status', 'absent')->count(),
            'late' => $todayAttendance->where('status', 'late')->count(),
            'half_day' => $todayAttendance->where('status', 'half_day')->count(),
        ];
        
        // Leave applications
        $leaveStats = [
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];
        
        // Next upcoming holiday
        $nextHoliday = Holiday::where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->first();
        
        // Department distribution
        $departmentDistribution = Department::withCount('employees')
            ->having('employees_count', '>', 0)
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name,
                    'count' => $dept->employees_count
                ];
            });
        
        // Recent activity (last 10 user logs)
        $recentActivity = UserLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $this->formatLogDescription($log),
                    'created_at' => $log->created_at->toISOString(),
                    'user' => $log->user ? $log->user->name : 'System'
                ];
            });
        
        // Employee activity chart data (last 7 days)
        $activityData = $this->getActivityChartData();
        
        return response()->json([
            'user' => $user,
            'stats' => [
                'totalEmployees' => $totalEmployees,
                'todayAttendance' => $attendanceStats,
                'leaveApplications' => $leaveStats,
                'nextHoliday' => $nextHoliday ? [
                    'name' => $nextHoliday->name,
                    'date' => $nextHoliday->date
                ] : null,
                'departmentDistribution' => $departmentDistribution,
                'recentActivity' => $recentActivity,
                'activityChartData' => $activityData
            ],
            'timestamp' => now(),
        ]);
    }
    
    private function formatLogDescription($log)
    {
        $type = $log->type;
        $user = $log->user ? $log->user->name : 'System';
        
        switch ($type) {
            case 'employee_create':
                return "New employee created by {$user}";
            case 'employee_update':
                return "Employee updated by {$user}";
            case 'attendance_create':
                return "Attendance marked by {$user}";
            case 'leave_application_submit':
                return "Leave application submitted by {$user}";
            case 'leave_application_approve':
                return "Leave application approved by {$user}";
            case 'leave_application_reject':
                return "Leave application rejected by {$user}";
            case 'microsoft_login':
                return "Microsoft login by {$user}";
            case 'microsoft_account_created':
                return "New Microsoft account created for {$user}";
            default:
                return "Activity performed by {$user}";
        }
    }
    
    private function getActivityChartData()
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $logins = UserLog::where('type', 'microsoft_login')
                ->whereDate('created_at', $date)
                ->count();
            $actions = UserLog::whereIn('type', [
                'employee_create', 'employee_update', 'attendance_create',
                'leave_application_submit', 'leave_application_approve'
            ])->whereDate('created_at', $date)->count();
            
            $last7Days->push([
                'date' => $date,
                'logins' => $logins,
                'actions' => $actions
            ]);
        }
        
        return $last7Days;
    }
}

