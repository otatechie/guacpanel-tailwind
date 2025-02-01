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
        $roles = Role::with(['permissions', 'users'])
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'users_count' => $role->users->count(),
                    'permissions' => $role->permissions,
                    'created_at' => $role->created_at->diffForHumans()
                ];
            });
        $users = User::get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->diffForHumans()
            ];
        });

        return Inertia::render('Admin/PermissionRole/IndexPermissionRolePage', [
            'permissions' => [
                'data' => $permissions
            ],
            'roles' => [
                'data' => $roles
            ],
            'users' => $users,
        ]);
    }
}
