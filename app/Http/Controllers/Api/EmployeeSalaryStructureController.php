<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSalaryStructure;
use App\Models\SalaryStructure;
use App\Models\UserLog;
use Illuminate\Http\Request;

class EmployeeSalaryStructureController extends Controller
{
	public function store(Request $request)
	{
		$validated = $request->validate([
			'employee_id' => ['required','exists:employees,id'],
			'salary_structure_id' => ['required','exists:salary_structures,id'],
			'custom_json' => ['nullable','array'],
			'effective_date' => ['required','date'],
		]);

		$ess = EmployeeSalaryStructure::create($validated);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'employee_salary_structure_assign',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['employee_id' => $validated['employee_id'], 'salary_structure_id' => $validated['salary_structure_id']],
		]);

		return response()->json($ess->load('structure.components'), 201);
	}

	public function showByEmployee(int $employeeId)
	{
		$ess = EmployeeSalaryStructure::with('structure.components')
			->where('employee_id', $employeeId)
			->orderByDesc('effective_date')
			->first();
		return $ess ?: response()->json(['message' => 'No salary structure assigned'], 404);
	}
}


