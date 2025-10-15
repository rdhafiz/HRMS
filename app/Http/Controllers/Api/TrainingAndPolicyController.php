<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingAndPolicy;
use App\Models\UserLog;
use Illuminate\Http\Request;

class TrainingAndPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $type = $request->query('type');
        
        $query = TrainingAndPolicy::query();
        
        if ($q) {
            $query->where(function($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
            });
        }
        
        if ($type && in_array($type, ['training', 'policy'])) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:training,policy',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:training_policy_categories,id',
        ]);

        $trainingAndPolicy = TrainingAndPolicy::create($validated);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'training_policy_create',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['training_policy_id' => $trainingAndPolicy->id],
        ]);

        return response()->json($trainingAndPolicy->load('category'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingAndPolicy $trainingAndPolicy)
    {
        return $trainingAndPolicy;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainingAndPolicy $trainingAndPolicy)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:training,policy',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:training_policy_categories,id',
        ]);

        $trainingAndPolicy->update($validated);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'training_policy_update',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['training_policy_id' => $trainingAndPolicy->id],
        ]);

        return response()->json($trainingAndPolicy->load('category'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, TrainingAndPolicy $trainingAndPolicy)
    {
        $trainingAndPolicy->delete();

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'training_policy_delete',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['training_policy_id' => $trainingAndPolicy->id],
        ]);

        return response()->json(['message' => 'Training/Policy deleted successfully']);
    }
}
