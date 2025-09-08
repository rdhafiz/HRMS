<?php

namespace App\Http\Controllers\Api;

use App\Constants\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\UserLog;
use Illuminate\Http\Request;
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

