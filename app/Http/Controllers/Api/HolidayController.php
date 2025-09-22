<?php

namespace App\Http\Controllers\Api;

use App\Constants\AttendanceStatus;
use App\Constants\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
	public function index(Request $request)
	{
		$query = Holiday::query();
		if ($request->filled('department_id')) {
			$query->where('department_id', $request->department_id);
		}
		if ($request->filled('start_date')) {
			$query->whereDate('start_date', '>=', $request->start_date);
		}
		if ($request->filled('end_date')) {
			$query->whereDate('end_date', '<=', $request->end_date);
		}
		return $query->orderByDesc('start_date')->paginate(10);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'department_id' => ['nullable', 'exists:departments,id'],
			'name' => ['required', 'string', 'max:191'],
			'start_date' => ['required', 'date'],
			'end_date' => ['required', 'date', 'after_or_equal:start_date'],
			'description' => ['nullable', 'string'],
		]);

		$holiday = Holiday::create($validated);

		$this->markHolidayInAttendance($holiday);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'holiday_create',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['holiday_id' => $holiday->id],
		]);

		return response()->json($holiday, 201);
	}

	public function update(Request $request, Holiday $holiday)
	{
		$validated = $request->validate([
			'department_id' => ['nullable', 'exists:departments,id'],
			'name' => ['required', 'string', 'max:191'],
			'start_date' => ['required', 'date'],
			'end_date' => ['required', 'date', 'after_or_equal:start_date'],
			'description' => ['nullable', 'string'],
		]);

		$holiday->update($validated);
		$this->markHolidayInAttendance($holiday, true);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'holiday_update',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['holiday_id' => $holiday->id],
		]);

		return response()->json($holiday);
	}

	public function destroy(Request $request, Holiday $holiday)
	{
		$holiday->delete();

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'holiday_delete',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['holiday_id' => $holiday->id],
		]);

		return response()->json(['message' => 'Deleted']);
	}

	public function employeeHolidays(Request $request)
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

		$query = Holiday::query();

		// Filter holidays based on:
		// 1. Global holidays (department_id is null)
		// 2. Department-specific holidays (department_id matches employee's department)
		$query->where(function ($q) use ($employee) {
			$q->whereNull('department_id') // Global holidays
			  ->orWhere('department_id', $employee->department_id); // Department holidays
		});

		// Optional date filtering
		if ($request->filled('start_date')) {
			$query->whereDate('start_date', '>=', $request->start_date);
		}
		if ($request->filled('end_date')) {
			$query->whereDate('end_date', '<=', $request->end_date);
		}

		// Order by start date (most recent first)
		$holidays = $query->orderBy('start_date', 'desc')->get();

		// Add status information to each holiday
		$holidays->transform(function ($holiday) {
			$today = now()->toDateString();
			$startDate = $holiday->start_date->toDateString();
			$endDate = $holiday->end_date->toDateString();

			// Determine holiday status
			if ($endDate < $today) {
				$holiday->status = 'past';
			} elseif ($startDate <= $today && $endDate >= $today) {
				$holiday->status = 'current';
			} else {
				$holiday->status = 'upcoming';
			}

			$holiday->is_past = $endDate < $today;
			$holiday->is_upcoming = $startDate > $today;
			$holiday->is_current = $startDate <= $today && $endDate >= $today;

			return $holiday;
		});

		return response()->json($holidays);
	}

	private function markHolidayInAttendance(Holiday $holiday, bool $update = false): void
	{
		$period = new \DatePeriod(new \DateTime($holiday->start_date), new \DateInterval('P1D'), (new \DateTime($holiday->end_date))->modify('+1 day'));

		$employeesQuery = Employee::query();
		if ($holiday->department_id) {
			$employeesQuery->where('department_id', $holiday->department_id);
		}
		$employeeIds = $employeesQuery->pluck('id');

		foreach ($period as $date) {
			foreach ($employeeIds as $empId) {
				Attendance::updateOrCreate(
					['employee_id' => $empId, 'date' => $date->format('Y-m-d')],
					['status' => AttendanceStatus::HOLIDAY, 'check_in' => null, 'check_out' => null, 'working_hours' => 0]
				);
			}
		}
	}
}

