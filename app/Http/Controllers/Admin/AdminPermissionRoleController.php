<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Traits\HasProtectedPermission;
use App\Traits\HasProtectedRoles;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPermissionRoleController extends Controller implements HasMiddleware
{
    use HasProtectedPermission;
    use HasProtectedRoles;

    public function __construct(private DataTableService $dataTable) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:view-permissions-roles|manage-roles|manage-permissions')];
    }

    public function index(Request $request)
    {
        $permissions = Permission::query()
            ->orderBy('name')
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'description' => $permission->description,
                    'created_at' => $permission->created_at->diffForHumans(),
                    'is_protected' => $this->isProtectedPermission($permission->name),
                ];
            });

        $roles = Role::with(['permissions:id,name,description'])
            ->get()
            ->map(
                fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'is_protected' => $this->isProtectedRole($role->name),
                    'permissions' => $role->permissions->map(
                        fn($permission) => [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'description' => $permission->description,
                        ],
                    ),
                ],
            );

        return Inertia::render('Admin/PermissionRole/IndexPermissionRolePage', [
            'permissions' => $permissions,
            'permissionsList' => $permissions->toArray(),
            'roles' => $roles,
            'protectedRoles' => $this->getProtectedRoles(),
            'protectedPermissions' => $this->getProtectedPermissions(),
            'filters' => $this->dataTable->buildFilters($request),
        ]);
    }
}
