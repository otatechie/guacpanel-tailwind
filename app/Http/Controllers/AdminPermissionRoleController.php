<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DataTablePaginationService;
use App\Traits\HasProtectedPermission;
use App\Traits\HasProtectedRoles;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPermissionRoleController extends Controller
{
    use HasProtectedRoles;
    use HasProtectedPermission;

    public function __construct(private DataTablePaginationService $pagination)
    {
        $this->middleware('permission:view-permissions-roles');
    }

    public function index(Request $request)
    {
        $perPage = $this->pagination->resolvePerPageWithDefaults($request);

        $permissions = Permission::query()
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($permission) {
                return [
                    'id'           => $permission->id,
                    'name'         => $permission->name,
                    'description'  => $permission->description,
                    'created_at'   => $permission->created_at->diffForHumans(),
                    'is_protected' => $this->isProtectedPermission($permission->name),
                ];
            });

        $roles = Role::with(['permissions', 'users'])->get();
        $users = User::all();

        return Inertia::render('Admin/PermissionRole/IndexPermissionRolePage', [
            'permissions'          => $permissions,
            'permissionsList'      => $permissions->items(),
            'roles'                => $roles,
            'users'                => $users,
            'protectedRoles'       => $this->getProtectedRoles(),
            'protectedPermissions' => $this->getProtectedPermissions(),
            'filters'              => $this->pagination->buildFilters($request),
        ]);
    }
}
