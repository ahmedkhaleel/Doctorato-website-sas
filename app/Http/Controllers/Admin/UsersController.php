<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::orderBy('created_at', 'desc')->get()->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'permissions' => $u->permissions ?? [],
                'is_active' => $u->is_active,
                'last_login_at' => $u->last_login_at,
                'created_at' => $u->created_at,
            ]),
            'roles' => User::roles(),
            'availablePermissions' => User::availablePermissions(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);
        $validated['permissions'] = $validated['permissions']
            ?? User::defaultPermissionsForRole($validated['role']);

        User::create($validated);
        return back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return back()->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي');
        }
        $user->delete();
        return back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الحالي');
        }
        $user->update(['is_active' => ! $user->is_active]);
        return back()->with('success', 'تم تحديث حالة المستخدم');
    }
}
