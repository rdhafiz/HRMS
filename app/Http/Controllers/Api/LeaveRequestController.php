<?php

namespace App\Http\Controllers\Api;

use App\Constants\LeaveTypes;
use App\Constants\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class LeaveRequestController extends Controller
{
	public function index(Request $request)
	{
		$query = LeaveRequest::query()->with(['employee', 'approver']);
		if ($request->filled('employee_id')) {
			$query->where('employee_id', $request->employee_id);
		}
		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}
		if ($request->filled('leave_type')) {
			$query->where('leave_type', $request->leave_type);
		}
		return $query->orderByDesc('id')->paginate(10);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'employee_id' => ['required', 'exists:employees,id'],
			'leave_type' => ['required', Rule::in(LeaveTypes::all())],
			'start_date' => ['required', 'date'],
			'end_date' => ['required', 'date', 'after_or_equal:start_date'],
			'reason' => ['nullable', 'string'],
		]);

		$lr = LeaveRequest::create(array_merge($validated, [
			'status' => 'pending',
		]));

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'leave_create',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['leave_request_id' => $lr->id],
		]);

		return response()->json($lr->load('employee'), 201);
	}

	public function update(Request $request, LeaveRequest $leaveRequest)
	{
		$validated = $request->validate([
			'leave_type' => ['required', Rule::in(LeaveTypes::all())],
			'start_date' => ['required', 'date'],
			'end_date' => ['required', 'date', 'after_or_equal:start_date'],
			'reason' => ['nullable', 'string'],
			'status' => ['required', Rule::in(['pending','approved','rejected'])],
		]);

		$leaveRequest->update($validated);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'leave_update',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['leave_request_id' => $leaveRequest->id],
		]);

		return response()->json($leaveRequest->load('employee'));
	}

	public function approve(Request $request, LeaveRequest $leaveRequest)
	{
		$request->validate([
			'action' => ['required', Rule::in(['approve','reject'])],
		]);

		$leaveRequest->status = $request->action === 'approve' ? 'approved' : 'rejected';
		$leaveRequest->approved_by = $request->user()->id;
		$leaveRequest->save();

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => $request->action === 'approve' ? 'leave_approve' : 'leave_reject',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['leave_request_id' => $leaveRequest->id],
		]);

		if ($leaveRequest->status === 'approved') {
			$this->markAttendanceAsLeave($leaveRequest);
		}

		return response()->json($leaveRequest->load('employee'));
	}

	public function destroy(Request $request, LeaveRequest $leaveRequest)
	{
		$leaveRequest->delete();
		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'leave_delete',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['leave_request_id' => $leaveRequest->id],
		]);
		return response()->json(['message' => 'Deleted']);
	}

	private function markAttendanceAsLeave(LeaveRequest $lr): void
	{
		$period = new \DatePeriod(new \DateTime($lr->start_date), new \DateInterval('P1D'), (new \DateTime($lr->end_date))->modify('+1 day'));
		foreach ($period as $date) {
			Attendance::updateOrCreate(
				['employee_id' => $lr->employee_id, 'date' => $date->format('Y-m-d')],
				['status' => AttendanceStatus::LEAVE, 'check_in' => null, 'check_out' => null, 'working_hours' => 0]
			);
		}
	}
}

