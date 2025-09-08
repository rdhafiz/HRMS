<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $departmentId = $request->query('department_id');
        $query = Designation::query()->with('department');
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        return $query->orderBy('name')->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);

        $designation = Designation::create([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'designation_create',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['designation_id' => $designation->id],
        ]);

        return response()->json($designation, 201);
    }

    public function show(Designation $designation)
    {
        return $designation->load('department');
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);

        $designation->update([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'designation_update',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['designation_id' => $designation->id],
        ]);

        return response()->json($designation);
    }

    public function destroy(Request $request, Designation $designation)
    {
        $designation->delete();

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'designation_delete',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['designation_id' => $designation->id],
        ]);

        return response()->json(['message' => 'Deleted']);
    }
}

