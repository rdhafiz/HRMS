<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
	public function index(Request $request)
	{
		$q = $request->query('q');
		$department_id = $request->query('department_id');
		$query = Employee::query()->with(['department', 'designation']);

		if ($q) {
			$query->where(function ($sub) use ($q) {
				$sub->where('first_name', 'like', "%{$q}%")
					->orWhere('last_name', 'like', "%{$q}%")
					->orWhere('email', 'like', "%{$q}%")
					->orWhere('employee_code', 'like', "%{$q}%");
			});
		}

		if ($department_id) {
			$query->where(function ($sub) use ($department_id) {
				$sub->where('department_id', $department_id);
			});
		}

		return $query->orderBy('employee_code')->paginate(10);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'first_name' => ['required', 'string', 'max:191'],
			'last_name' => ['required', 'string', 'max:191'],
			'email' => ['required', 'email', 'max:191', 'unique:employees,email'],
			'phone' => ['nullable', 'string', 'max:191'],
			'date_of_birth' => ['nullable', 'date'],
			'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
			'address' => ['nullable', 'string'],

			'employee_code' => ['required', 'string', 'max:191', 'unique:employees,employee_code'],
			'join_date' => ['required', 'date'],
			'department_id' => ['required', 'exists:departments,id'],
			'designation_id' => ['required', 'exists:designations,id'],
			'status' => ['nullable', Rule::in(['active', 'inactive', 'terminated'])],
			'salary' => ['nullable', 'numeric'],
		]);

		$employee = Employee::create(array_merge($validated, [
			'created_by' => $request->user()->id,
			'status' => $validated['status'] ?? 'active',
		]));

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'employee_create',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['employee_id' => $employee->id],
		]);

		return response()->json($employee->load(['department', 'designation']), 201);
	}

	public function show(Employee $employee)
	{
		return $employee->load(['department', 'designation']);
	}

	public function update(Request $request, Employee $employee)
	{
		$validated = $request->validate([
			'first_name' => ['required', 'string', 'max:191'],
			'last_name' => ['required', 'string', 'max:191'],
			'email' => ['required', 'email', 'max:191', Rule::unique('employees', 'email')->ignore($employee->id)],
			'phone' => ['nullable', 'string', 'max:191'],
			'date_of_birth' => ['nullable', 'date'],
			'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
			'address' => ['nullable', 'string'],

			'employee_code' => ['required', 'string', 'max:191', Rule::unique('employees', 'employee_code')->ignore($employee->id)],
			'join_date' => ['required', 'date'],
			'department_id' => ['required', 'exists:departments,id'],
			'designation_id' => ['required', 'exists:designations,id'],
			'status' => ['nullable', Rule::in(['active', 'inactive', 'terminated'])],
			'salary' => ['nullable', 'numeric'],
		]);

		$employee->update($validated);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'employee_update',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['employee_id' => $employee->id],
		]);

		return response()->json($employee->load(['department', 'designation']));
	}

	public function destroy(Request $request, Employee $employee)
	{
		$employee->delete();

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'employee_delete',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['employee_id' => $employee->id],
		]);

		return response()->json(['message' => 'Deleted']);
	}
}


