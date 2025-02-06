<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return Inertia::render('Admin/User/IndexUserPage', [
            'users' => $users,
        ]);
    }


    public function edit($id)
    {
        $user = User::with(['permissions', 'roles'])->findOrFail($id);

        return Inertia::render('Admin/User/EditUserPage', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'force_password_change' => $user->force_password_change,
                'roles' => $user->roles,
                'permissions' => $user->permissions->map(fn($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ])
            ],
            'permissions' => [
                'data' => Permission::select(['id', 'name'])->get()
            ],
            'roles' => [
                'data' => Role::select(['id', 'name'])->get()
            ],
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'role' => ['required', 'exists:roles,id'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
            'is_active' => ['boolean'],
            'force_password_change' => ['boolean'],
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
            'force_password_change' => $request->force_password_change,
        ]);

        $user->syncRoles([$request->role]);
        $user->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'User updated successfully');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
