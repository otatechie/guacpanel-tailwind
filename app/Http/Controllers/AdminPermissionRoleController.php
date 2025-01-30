<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminPermissionRoleController extends Controller
{
    public function index()
    {
        $permissions = Permission::get();
        $roles = Role::get();
        $users = User::get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->diffForHumans()
            ];
        });

        return Inertia::render('Admin/PermissionRole/IndexPermissionRolePage', [
            'permissions' => $permissions,
            'roles' => $roles,
            'users' => $users,
        ]);
    }


    public function storeRole(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string',  Rule::unique(Role::class)],
        ]);

        Role::create($validatedData);

        session()->flash('success', 'Role created successfully.');
    }


    public function updateRole(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string',  Rule::unique('campaigns', 'title')->ignore($role->id)],
        ]);

        $role->update($validatedData);

        session()->flash('success', 'Role updated successfully.');
    }
}
