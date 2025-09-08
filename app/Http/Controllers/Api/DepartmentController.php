<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Department::query()->withCount('designations');
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }
        return $query->orderBy('name')->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:departments,name',
            'description' => 'nullable|string',
        ]);

        $department = Department::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'department_create',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['department_id' => $department->id],
        ]);

        return response()->json($department, 201);
    }

    public function show(Department $department)
    {
        return $department->loadCount('designations');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'department_update',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['department_id' => $department->id],
        ]);

        return response()->json($department);
    }

    public function destroy(Request $request, Department $department)
    {
        $department->delete();

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'department_delete',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => ['department_id' => $department->id],
        ]);

        return response()->json(['message' => 'Deleted']);
    }
}

