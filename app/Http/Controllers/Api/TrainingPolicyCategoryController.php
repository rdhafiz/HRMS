<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingPolicyCategory;
use App\Models\UserLog;
use Illuminate\Http\Request;

class TrainingPolicyCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $type = $request->query('type');
        
        $query = TrainingPolicyCategory::with('parent');
        
        if ($q) {
            $query->where('title', 'like', "%{$q}%");
        }
        
        if ($type && in_array($type, ['training', 'policy'])) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('type')->orderBy('parent_id')->orderBy('title')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:training,policy',
            'parent_id' => 'nullable|exists:training_policy_categories,id',
        ]);

        // Validate that parent category is of the same type
        if ($validated['parent_id']) {
            $parent = TrainingPolicyCategory::find($validated['parent_id']);
            if ($parent && $parent->type !== $validated['type']) {
                return response()->json([
                    'message' => 'Parent category must be of the same type.',
                    'errors' => ['parent_id' => ['Parent category must be of the same type.']]
                ], 422);
            }
        }

        $category = TrainingPolicyCategory::create($validated);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'training_policy_category_create',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['category_id' => $category->id],
        ]);

        return response()->json($category->load('parent'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingPolicyCategory $trainingPolicyCategory)
    {
        return $trainingPolicyCategory->load(['parent', 'children']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainingPolicyCategory $trainingPolicyCategory)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:training,policy',
            'parent_id' => 'nullable|exists:training_policy_categories,id',
        ]);

        // Prevent setting self as parent
        if ($validated['parent_id'] == $trainingPolicyCategory->id) {
            return response()->json([
                'message' => 'Category cannot be its own parent.',
                'errors' => ['parent_id' => ['Category cannot be its own parent.']]
            ], 422);
        }

        // Validate that parent category is of the same type
        if ($validated['parent_id']) {
            $parent = TrainingPolicyCategory::find($validated['parent_id']);
            if ($parent && $parent->type !== $validated['type']) {
                return response()->json([
                    'message' => 'Parent category must be of the same type.',
                    'errors' => ['parent_id' => ['Parent category must be of the same type.']]
                ], 422);
            }
        }

        $trainingPolicyCategory->update($validated);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'training_policy_category_update',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['category_id' => $trainingPolicyCategory->id],
        ]);

        return response()->json($trainingPolicyCategory->load('parent'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, TrainingPolicyCategory $trainingPolicyCategory)
    {
        // Check if category has children
        if ($trainingPolicyCategory->children()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with subcategories. Please delete subcategories first.',
                'errors' => ['general' => ['Cannot delete category with subcategories. Please delete subcategories first.']]
            ], 422);
        }

        $trainingPolicyCategory->delete();

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'training_policy_category_delete',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['category_id' => $trainingPolicyCategory->id],
        ]);

        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Get parent categories for dropdown.
     */
    public function getParents(Request $request)
    {
        $type = $request->query('type');
        
        $query = TrainingPolicyCategory::parents();
        
        if ($type && in_array($type, ['training', 'policy'])) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('title')->get(['id', 'title', 'type']);
    }
}
