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
                'disable_account' => $user->disable_account,
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
            'role' => ['nullable', 'exists:roles,id'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
            'disable_account' => ['boolean'],
            'force_password_change' => ['boolean'],
        ]);

        if ($request->force_password_change && $request->disable_account) {
            return back()->withErrors(['error' => 'User cannot be both disabled and forced to change password']);
        }

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'force_password_change' => $request->force_password_change,
            'disable_account' => $request->disable_account,
        ]);

        $user->syncRoles([$request->role]);
        $user->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'User account updated successfully');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
