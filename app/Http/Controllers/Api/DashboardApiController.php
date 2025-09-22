<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use App\Models\Department;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    /**
     * Get employee count statistics
     */
    public function employees()
    {
        try {
            $totalEmployees = Employee::count();
            
            // Calculate growth percentage (mock data for now)
            $lastMonth = Employee::where('created_at', '>=', now()->subMonth())->count();
            $previousMonth = Employee::whereBetween('created_at', [
                now()->subMonths(2),
                now()->subMonth()
            ])->count();
            
            $growthPercentage = $previousMonth > 0 
                ? round((($lastMonth - $previousMonth) / $previousMonth) * 100, 1)
                : 0;

            return response()->json([
                'totalEmployees' => $totalEmployees,
                'growthPercentage' => $growthPercentage
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load employee data'], 500);
        }
    }

    /**
     * Get today's attendance summary
     */
    public function attendance()
    {
        try {
            $today = now()->toDateString();
            $todayAttendance = Attendance::whereDate('date', $today)->get();
            
            $totalEmployees = Employee::count();
            
            $stats = [
                'present' => $todayAttendance->where('status', 'present')->count(),
                'absent' => $todayAttendance->where('status', 'absent')->count(),
                'late' => $todayAttendance->where('status', 'late')->count(),
                'halfDay' => $todayAttendance->where('status', 'half_day')->count(),
                'totalEmployees' => $totalEmployees
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load attendance data'], 500);
        }
    }

    /**
     * Get leave applications summary
     */
    public function leaves()
    {
        try {
            $stats = [
                'pending' => LeaveRequest::where('status', 'pending')->count(),
                'approved' => LeaveRequest::where('status', 'approved')->count(),
                'rejected' => LeaveRequest::where('status', 'rejected')->count()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load leave data'], 500);
        }
    }

    /**
     * Get upcoming holiday information
     */
    public function holidays()
    {
        try {
            $today = now()->toDateString();
            $nextHoliday = Holiday::where('start_date', '>=', $today)
                ->orderBy('start_date', 'asc')
                ->first();

            if (!$nextHoliday) {
                return response()->json([
                    'name' => null,
                    'date' => null,
                    'daysUntil' => null
                ]);
            }

            $daysUntil = Carbon::parse($nextHoliday->start_date)->diffInDays(now());

            return response()->json([
                'name' => $nextHoliday->name,
                'date' => $nextHoliday->start_date,
                'daysUntil' => $daysUntil
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load holiday data'], 500);
        }
    }

    /**
     * Get employee activity data for charts
     */
    public function activity(Request $request)
    {
        try {
            $period = $request->query('period', '7d');
            
            if ($period === '7d') {
                $data = $this->getActivityData7Days();
            } else {
                $data = $this->getActivityData30Days();
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load activity data'], 500);
        }
    }

    /**
     * Get department distribution
     */
    public function departments()
    {
        try {
            $departments = Department::withCount('employees')
                ->having('employees_count', '>', 0)
                ->get()
                ->map(function ($dept) {
                    return [
                        'name' => $dept->name,
                        'count' => $dept->employees_count
                    ];
                });

            return response()->json($departments);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load department data'], 500);
        }
    }

    /**
     * Get recent activity logs
     */
    public function activityLogs()
    {
        try {
            $activities = UserLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'description' => $this->formatLogDescription($log),
                        'created_at' => $log->created_at->toISOString(),
                        'user' => $log->user ? $log->user->name : 'System'
                    ];
                });

            return response()->json($activities);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to load activity logs'], 500);
        }
    }

    private function getActivityData7Days()
    {
        $labels = [];
        $logins = [];
        $actions = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->toDateString();
            
            $labels[] = $date->format('D');
            
            $loginCount = UserLog::where('type', 'microsoft_login')
                ->whereDate('created_at', $dateStr)
                ->count();
            $logins[] = $loginCount;
            
            $actionCount = UserLog::whereIn('type', [
                'employee_create', 'employee_update', 'attendance_create',
                'leave_application_submit', 'leave_application_approve'
            ])->whereDate('created_at', $dateStr)->count();
            $actions[] = $actionCount;
        }

        return [
            'labels' => $labels,
            'logins' => $logins,
            'actions' => $actions
        ];
    }

    private function getActivityData30Days()
    {
        $labels = [];
        $logins = [];
        $actions = [];

        for ($i = 3; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i + 1);
            $endDate = now()->subWeeks($i);
            
            $labels[] = "Week " . (4 - $i);
            
            $loginCount = UserLog::where('type', 'microsoft_login')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $logins[] = $loginCount;
            
            $actionCount = UserLog::whereIn('type', [
                'employee_create', 'employee_update', 'attendance_create',
                'leave_application_submit', 'leave_application_approve'
            ])->whereBetween('created_at', [$startDate, $endDate])->count();
            $actions[] = $actionCount;
        }

        return [
            'labels' => $labels,
            'logins' => $logins,
            'actions' => $actions
        ];
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
}