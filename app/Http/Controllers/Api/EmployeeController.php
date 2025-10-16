<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalaryStructure;
use App\Models\EmployeeTrainingPolicy;
use App\Models\TrainingPolicyCategory;
use App\Models\User;
use App\Models\UserLog;
use App\Constants\UserRoles;
use App\Mail\EmployeeWelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
			'email' => ['required', 'email', 'max:191', 'unique:employees,email', 'unique:users,email'],
			'phone' => ['nullable', 'string', 'max:191'],
			'date_of_birth' => ['nullable', 'date'],
			'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
			'address' => ['nullable', 'string'],

			'employee_code' => ['required', 'string', 'max:191', 'unique:employees,employee_code'],
			'join_date' => ['required', 'date'],
			'department_id' => ['required', 'exists:departments,id'],
			'designation_id' => ['required', 'exists:designations,id'],
			'status' => ['nullable', Rule::in(['active', 'inactive', 'terminated'])],
			
			// Salary structure fields
			'salary_structure_id' => ['required', 'exists:salary_structures,id'],
			'effective_date' => ['required', 'date'],
			
			// User account fields
			'password' => ['required', 'string', 'min:8', 'confirmed'],
			'password_confirmation' => ['required', 'string', 'min:8'],
		]);

		return DB::transaction(function () use ($request, $validated) {
			// Create employee record
			$employee = Employee::create([
				'first_name' => $validated['first_name'],
				'last_name' => $validated['last_name'],
				'email' => $validated['email'],
				'phone' => $validated['phone'],
				'date_of_birth' => $validated['date_of_birth'],
				'gender' => $validated['gender'],
				'address' => $validated['address'],
				'employee_code' => $validated['employee_code'],
				'join_date' => $validated['join_date'],
				'department_id' => $validated['department_id'],
				'designation_id' => $validated['designation_id'],
				'status' => $validated['status'] ?? 'active',
				'created_by' => $request->user()->id,
			]);

			// Create user account for employee
			$user = User::create([
				'name' => $employee->name,
				'email' => $validated['email'],
				'password' => Hash::make($validated['password']),
				'admin_type' => UserRoles::EMPLOYEE,
				'email_verified_at' => now(), // Auto-verify employee accounts
			]);

			// Assign salary structure
			EmployeeSalaryStructure::create([
				'employee_id' => $employee->id,
				'salary_structure_id' => $validated['salary_structure_id'],
				'effective_date' => $validated['effective_date'],
			]);

			// Send welcome email with login credentials
			Mail::to($employee->email)->send(new EmployeeWelcomeMail($employee, $validated['password']));

			UserLog::create([
				'user_id' => $request->user()->id,
				'type' => 'employee_create',
				'ip' => $request->ip(),
				'user_agent' => (string) $request->userAgent(),
				'meta' => ['employee_id' => $employee->id, 'user_id' => $user->id],
			]);

			return response()->json($employee->load(['department', 'designation', 'currentSalaryStructure.structure.components']), 201);
		});
	}

	public function show(Employee $employee)
	{
		return $employee->load(['department', 'designation', 'currentSalaryStructure.structure.components']);
	}

	/**
	 * Get training policies for a specific employee (Admin/HR access)
	 */
	public function trainingPolicies(Employee $employee)
	{
		try {
			// Verify the employee has a user account
			if (!$employee->user) {
				return response()->json(['error' => 'Employee user account not found'], 404);
			}

			// Get completed training policy IDs for this employee
			$completedIds = EmployeeTrainingPolicy::where('employee_user_id', $employee->user_id)
				->pluck('training_policy_id')
				->toArray();

			// Get all categories with their training policies
			$categories = TrainingPolicyCategory::with([
				'trainingsAndPolicies' => function ($query) {
					$query->orderBy('type')->orderBy('title');
				},
				'children' => function ($query) {
					$query->with(['trainingsAndPolicies' => function ($query) {
						$query->orderBy('type')->orderBy('title');
					}]);
				}
			])
			->parents()
			->orderBy('type')
			->orderBy('title')
			->get();

			// Transform data to include completion status
			$categories = $categories->map(function ($category) use ($completedIds) {
				// Handle parent category trainings and policies
				$category->trainings_and_policies = $category->trainingsAndPolicies->map(function ($trainingPolicy) use ($completedIds) {
					$trainingPolicy->completed = in_array($trainingPolicy->id, $completedIds);
					return $trainingPolicy;
				});

				// Handle child category trainings and policies
				$category->children = $category->children->map(function ($child) use ($completedIds) {
					$child->trainings_and_policies = $child->trainingsAndPolicies->map(function ($trainingPolicy) use ($completedIds) {
						$trainingPolicy->completed = in_array($trainingPolicy->id, $completedIds);
						return $trainingPolicy;
					});
					return $child;
				});

				return $category;
			});

			return response()->json([
				'success' => true,
				'data' => $categories,
				'message' => 'Training policies retrieved successfully'
			]);

		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Failed to retrieve training policies',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function update(Request $request, Employee $employee)
	{
		$validationRules = [
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
			
			// Salary structure fields
			'salary_structure_id' => ['required', 'exists:salary_structures,id'],
			'effective_date' => ['required', 'date'],
		];

		// Add password validation only if password is provided
		if ($request->filled('password')) {
			$validationRules['password'] = ['string', 'min:8', 'confirmed'];
			$validationRules['password_confirmation'] = ['string', 'min:8'];
		}

		$validated = $request->validate($validationRules);

		return DB::transaction(function () use ($request, $employee, $validated) {
			$employee->update([
				'first_name' => $validated['first_name'],
				'last_name' => $validated['last_name'],
				'email' => $validated['email'],
				'phone' => $validated['phone'],
				'date_of_birth' => $validated['date_of_birth'],
				'gender' => $validated['gender'],
				'address' => $validated['address'],
				'employee_code' => $validated['employee_code'],
				'join_date' => $validated['join_date'],
				'department_id' => $validated['department_id'],
				'designation_id' => $validated['designation_id'],
				'status' => $validated['status'] ?? 'active',
			]);

			// Update user account if it exists
			$user = User::where('email', $employee->getOriginal('email'))->first();
			if ($user) {
				$userData = [
					'name' => $employee->name,
					'email' => $validated['email'],
				];

				// Only update password if provided
				if (isset($validated['password'])) {
					$userData['password'] = Hash::make($validated['password']);
				}

				$user->update($userData);
			} else {
				// Create user account for employee
				$user = User::create([
					'name' => $employee->name,
					'email' => $employee->email,
					'password' => Hash::make($validated['password']),
					'admin_type' => UserRoles::EMPLOYEE,
					'email_verified_at' => now(), // Auto-verify employee accounts
				]);
			}

			// Update or create salary structure assignment
			$currentSalaryStructure = $employee->currentSalaryStructure;
		
			if ($currentSalaryStructure && 
				$currentSalaryStructure->salary_structure_id == $validated['salary_structure_id'] &&
				date('Y-m-d', strtotime($currentSalaryStructure->effective_date)) == date('Y-m-d', strtotime($validated['effective_date']))) {
				// No change needed
			} else {
				// Create new salary structure assignment
				EmployeeSalaryStructure::create([
					'employee_id' => $employee->id,
					'salary_structure_id' => $validated['salary_structure_id'],
					'effective_date' => $validated['effective_date'],
				]);
			}

			UserLog::create([
				'user_id' => $request->user()->id,
				'type' => 'employee_update',
				'ip' => $request->ip(),
				'user_agent' => (string) $request->userAgent(),
				'meta' => ['employee_id' => $employee->id],
			]);

			return response()->json($employee->load(['department', 'designation', 'currentSalaryStructure.structure.components']));
		});
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


