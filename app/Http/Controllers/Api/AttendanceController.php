<?php

namespace App\Http\Controllers\Api;

use App\Constants\AttendanceStatus;
use App\Constants\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
	public function index(Request $request)
	{
		$date = $request->query('date');
		$employeeId = $request->query('employee_id');
		$departmentId = $request->query('department_id');

		$query = Attendance::query()->with(['employee.department', 'employee.designation']);

		if ($date) {
			$query->whereDate('date', $date);
		}
		if ($employeeId) {
			$query->where('employee_id', $employeeId);
		}
		if ($departmentId) {
			$query->whereHas('employee', function ($q) use ($departmentId) {
				$q->where('department_id', $departmentId);
			});
		}

		return $query->orderByDesc('date')->paginate(10);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'employee_id' => ['required', 'exists:employees,id'],
			'date' => ['required', 'date'],
			'check_in' => ['required'],
			'check_out' => ['required'],
			'status' => ['required', Rule::in(AttendanceStatus::all())],
			'remarks' => ['nullable', 'string'],
		]);

		$data = $validated;
		$validated['check_in'] = $validated['date'] . ' ' . $validated['check_in'];
		$validated['check_out'] = $validated['date'] . ' ' . $validated['check_out'];
		$data['working_hours'] = $this->calculateWorkingHours($validated['check_in'] ?? null, $validated['check_out'] ?? null);

		$attendance = Attendance::updateOrCreate(
			['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
			$data
		);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'attendance_create',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['attendance_id' => $attendance->id],
		]);

		return response()->json($attendance->load('employee'), 201);
	}

	public function update(Request $request, Attendance $attendance)
	{
		$validated = $request->validate([
			'check_in' => ['nullable', 'date'],
			'check_out' => ['nullable', 'date', 'after_or_equal:check_in'],
			'status' => ['required', Rule::in(AttendanceStatus::all())],
			'remarks' => ['nullable', 'string'],
		]);

		$data = $validated;
		$data['working_hours'] = $this->calculateWorkingHours($validated['check_in'] ?? $attendance->check_in, $validated['check_out'] ?? $attendance->check_out);

		$attendance->update($data);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'attendance_update',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['attendance_id' => $attendance->id],
		]);

		return response()->json($attendance->load('employee'));
	}

	public function destroy(Request $request, Attendance $attendance)
	{
		$attendance->delete();

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'attendance_delete',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['attendance_id' => $attendance->id],
		]);

		return response()->json(['message' => 'Deleted']);
	}

	public function employeeAttendance(Request $request)
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

		$query = Attendance::query()->where('employee_id', $employee->id);

		// Date range filtering
		if ($request->filled('start_date')) {
			$query->whereDate('date', '>=', $request->start_date);
		}
		if ($request->filled('end_date')) {
			$query->whereDate('date', '<=', $request->end_date);
		}

		// Status filtering
		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		// Order by date (most recent first)
		$attendances = $query->orderBy('date', 'desc')->get();

		// Add formatted time and status information
		$attendances->transform(function ($attendance) {
			$attendance->check_in_time = $attendance->check_in ? $attendance->check_in->format('H:i') : null;
			$attendance->check_out_time = $attendance->check_out ? $attendance->check_out->format('H:i') : null;
			$attendance->status_label = $this->getStatusLabel($attendance->status);
			$attendance->status_color = $this->getStatusColor($attendance->status);
			
			return $attendance;
		});

		return response()->json($attendances);
	}

	private function getStatusLabel($status): string
	{
		$labels = [
			AttendanceStatus::PRESENT => 'Present',
			AttendanceStatus::ABSENT => 'Absent',
			AttendanceStatus::LATE => 'Late',
			AttendanceStatus::HALF_DAY => 'Half Day',
			AttendanceStatus::LEAVE => 'Leave',
			AttendanceStatus::HOLIDAY => 'Holiday',
		];

		return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
	}

	private function getStatusColor($status): string
	{
		$colors = [
			AttendanceStatus::PRESENT => 'green',
			AttendanceStatus::ABSENT => 'red',
			AttendanceStatus::LATE => 'yellow',
			AttendanceStatus::HALF_DAY => 'blue',
			AttendanceStatus::LEAVE => 'purple',
			AttendanceStatus::HOLIDAY => 'gray',
		];

		return $colors[$status] ?? 'gray';
	}

	private function calculateWorkingHours($checkIn, $checkOut): float
	{
		if (!$checkIn || !$checkOut) {
			return 0.0;
		}
		$start = strtotime($checkIn);
		$end = strtotime($checkOut);
		if ($end <= $start) {
			return 0.0;
		}
		$hours = ($end - $start) / 3600;
		return round($hours, 2);
	}
}

