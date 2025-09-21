<?php

namespace App\Http\Controllers\Api;

use App\Constants\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
	public function index(Request $request)
	{
		$adminType = $request->query('admin_type');
		$q = $request->query('q');

		$query = User::query()->when($adminType, function ($q2) use ($adminType) {
			$q2->where('admin_type', (int) $adminType);
		});

		if ($q) {
			$query->where(function ($sub) use ($q) {
				$sub->where('name', 'like', "%{$q}%")
					->orWhere('email', 'like', "%{$q}%");
			});
		}

		return $query->orderBy('name')->paginate(10);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:191'],
			'email' => ['required', 'email', 'max:191', 'unique:users,email'],
			'password' => ['required', 'string', 'min:6'],
			'avatar' => ['nullable', 'file', 'image', 'max:2048'],
			'admin_type' => ['required', Rule::in([UserRoles::SYSTEM_ADMIN, UserRoles::HR_MANAGER, UserRoles::SUPERVISOR])],
		]);

		$data = $validated;
		if ($request->hasFile('avatar')) {
			$data['avatar'] = $request->file('avatar')->store('avatars', 'public');
		}

		$user = User::create($data);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'admin_create',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['target_user_id' => $user->id],
		]);

		return response()->json($user, 201);
	}

	public function show(User $admin)
	{
		return $admin;
	}

	public function update(Request $request, User $admin)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:191'],
			'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($admin->id)],
			'password' => ['nullable', 'string', 'min:6'],
			'avatar' => ['nullable', 'file', 'image', 'max:2048'],
			'admin_type' => ['required', Rule::in([UserRoles::SYSTEM_ADMIN, UserRoles::HR_MANAGER, UserRoles::SUPERVISOR])],
		]);

		$data = $validated;
		if (!isset($data['password'])) {
			unset($data['password']);
		}
		if ($request->hasFile('avatar')) {
			$data['avatar'] = $request->file('avatar')->store('avatars', 'public');
		}

		$admin->update($data);

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'admin_update',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['target_user_id' => $admin->id],
		]);

		return response()->json($admin);
	}

	public function destroy(Request $request, User $admin)
	{
		$admin->delete();

		UserLog::create([
			'user_id' => $request->user()->id,
			'type' => 'admin_delete',
			'ip' => $request->ip(),
			'user_agent' => (string) $request->userAgent(),
			'meta' => ['target_user_id' => $admin->id],
		]);

		return response()->json(['message' => 'Deleted']);
	}
}

