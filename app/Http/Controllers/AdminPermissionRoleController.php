<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HasProtectedRoles;
use App\Traits\HasProtectedPermission;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminPermissionRoleController extends Controller
{
    use HasProtectedRoles, HasProtectedPermission;
    

    public function __construct()
    {
        $this->middleware('permission:view-permissions-roles');
    }
    

    public function index()
    {
        $permissions = Permission::select('id', 'name', 'description', 'created_at')
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'description' => $permission->description,
                    'created_at' => $permission->created_at->diffForHumans(),
                    'is_protected' => $this->isProtectedPermission($permission->name)
                ];
            });

        $roles = Role::select('id', 'name', 'created_at')
            ->with([
                'permissions:id,name,description', 
                'users' => function($query) {
                    $query->select('id');
                }
            ])
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'users_count' => $role->users->count(),
                    'permissions' => $role->permissions->map(function($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'description' => $permission->description
                        ];
                    }),
                    'created_at' => $role->created_at->diffForHumans(),
                    'is_protected' => $this->isProtectedRole($role->name)
                ];
            });

        $users = User::select('id', 'name', 'email', 'created_at')
            ->get()
            ->map(function ($user) {
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
            'protectedRoles' => $this->getProtectedRoles(),
            'protectedPermissions' => $this->getProtectedPermissions(),
        ]);
    }
}
