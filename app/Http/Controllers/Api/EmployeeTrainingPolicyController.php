<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTrainingPolicy;
use App\Models\TrainingAndPolicy;
use App\Models\TrainingPolicyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeTrainingPolicyController extends Controller
{
    /**
     * Get all training policies with completion status for the authenticated employee.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Get employee record
            $employee = $user->employee;
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found'], 404);
            }

            // Get completed training policy IDs for this employee
            $completedIds = EmployeeTrainingPolicy::where('employee_user_id', $user->id)
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
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch training policies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save employee training policy completion status.
     */
    public function save(Request $request)
    {
        try {
            $request->validate([
                'training_policy_ids' => 'required|array',
                'training_policy_ids.*' => 'integer|exists:trainings_and_policies,id'
            ]);

            $user = Auth::user();
            
            // Get employee record
            $employee = $user->employee;
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found'], 404);
            }

            DB::beginTransaction();

            try {
                // Remove all existing completions for this employee
                EmployeeTrainingPolicy::where('employee_user_id', $user->id)->delete();

                // Add new completions
                $completions = [];
                foreach ($request->training_policy_ids as $trainingPolicyId) {
                    $completions[] = [
                        'employee_user_id' => $user->id,
                        'employee_id' => $employee->id,
                        'training_policy_id' => $trainingPolicyId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($completions)) {
                    EmployeeTrainingPolicy::insert($completions);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Training policy progress saved successfully'
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save training policy progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
