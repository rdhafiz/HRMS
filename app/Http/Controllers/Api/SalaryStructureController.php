<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryStructureController extends Controller
{
	public function index(Request $request)
	{
		$q = $request->query('q');
		$isTemplate = $request->query('is_template');
		$all = $request->query('all', false); // For dropdown usage
		
		$query = SalaryStructure::query()->with('components');
		if ($q) {
			$query->where('name', 'like', "%{$q}%");
		}
		if (!is_null($isTemplate)) {
			$query->where('is_template', filter_var($isTemplate, FILTER_VALIDATE_BOOLEAN));
		}
		
		if ($all) {
			return $query->orderBy('name')->get();
		}
		
		return $query->orderBy('name')->paginate(10);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required','string','max:191'],
			'description' => ['nullable','string'],
			'is_template' => ['required','boolean'],
			'components' => ['required','array','min:1'],
			'components.*.type' => ['required','in:basic,allowance,deduction'],
			'components.*.name' => ['required','string','max:191'],
			'components.*.amount' => ['required','numeric','gt:0'],
		]);

		if (!collect($validated['components'])->contains(fn($c) => $c['type'] === 'basic')) {
			return response()->json(['message' => 'Basic Pay is required as a component.'], 422);
		}

		return DB::transaction(function () use ($request, $validated) {
			$structure = SalaryStructure::create([
				'name' => $validated['name'],
				'description' => $validated['description'] ?? null,
				'is_template' => $validated['is_template'],
				'created_by' => $request->user()->id,
			]);
			foreach ($validated['components'] as $comp) {
				SalaryComponent::create([
					'salary_structure_id' => $structure->id,
					'type' => $comp['type'],
					'name' => $comp['name'],
					'amount' => $comp['amount'],
				]);
			}

			UserLog::create([
				'user_id' => $request->user()->id,
				'type' => 'salary_structure_create',
				'ip' => $request->ip(),
				'user_agent' => (string) $request->userAgent(),
				'meta' => ['salary_structure_id' => $structure->id],
			]);

			return response()->json($structure->load('components'), 201);
		});
	}

	public function show(SalaryStructure $salaryStructure)
	{
		return $salaryStructure->load('components');
	}

	public function update(Request $request, SalaryStructure $salaryStructure)
	{
		$validated = $request->validate([
			'name' => ['required','string','max:191'],
			'description' => ['nullable','string'],
			'is_template' => ['required','boolean'],
			'components' => ['required','array','min:1'],
			'components.*.type' => ['required','in:basic,allowance,deduction'],
			'components.*.name' => ['required','string','max:191'],
			'components.*.amount' => ['required','numeric','gt:0'],
		]);

		if (!collect($validated['components'])->contains(fn($c) => $c['type'] === 'basic')) {
			return response()->json(['message' => 'Basic Pay is required as a component.'], 422);
		}

		return DB::transaction(function () use ($request, $salaryStructure, $validated) {
			$salaryStructure->update([
				'name' => $validated['name'],
				'description' => $validated['description'] ?? null,
				'is_template' => $validated['is_template'],
			]);
			$salaryStructure->components()->delete();
			foreach ($validated['components'] as $comp) {
				SalaryComponent::create([
					'salary_structure_id' => $salaryStructure->id,
					'type' => $comp['type'],
					'name' => $comp['name'],
					'amount' => $comp['amount'],
				]);
			}

			UserLog::create([
				'user_id' => $request->user()->id,
				'type' => 'salary_structure_update',
				'ip' => $request->ip(),
				'user_agent' => (string) $request->userAgent(),
				'meta' => ['salary_structure_id' => $salaryStructure->id],
			]);

			return response()->json($salaryStructure->load('components'));
		});
	}

	public function destroy(Request $request, SalaryStructure $salaryStructure)
	{
		$salaryStructure->delete();
		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'salary_structure_delete',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['salary_structure_id' => $salaryStructure->id],
		]);
		return response()->json(['message' => 'Deleted']);
	}
}


