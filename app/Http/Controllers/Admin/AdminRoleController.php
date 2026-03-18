<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Traits\HasProtectedRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    use HasProtectedRoles;

    public function __construct(private DataTableService $dataTable)
    {
        $this->middleware(['auth', 'permission:manage-roles']);
    }

    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: Role::query()->with('permissions'),
            request: $request,
            config: [
                'searchable' => ['name', 'description', 'permissions.name'],
                'sortable' => [
                    'name' => ['type' => 'simple'],
                    'created_at' => ['type' => 'simple'],
                ],
                'resource' => 'roles',
                'transform' => function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'description' => $role->description,
                        'created_at' => $role->created_at?->diffForHumans(),
                        'is_protected' => $this->isProtectedRole($role->name),
                        'permissions' => $role->permissions->map(function ($permission) {
                            return [
                                'id' => $permission->id,
                                'name' => $permission->name,
                            ];
                        }),
                    ];
                },
            ],
        );

        return Inertia::render('Admin/PermissionRole/IndexPermissionRolePage', [
            'roles' => $result['data'],
            'permissions' => Permission::all(),
            'filters' => $result['filters'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('roles', 'name'),
                'not_in:' . $this->getProtectedRolesForValidation(),
                'regex:/^[a-zA-Z][a-zA-Z0-9\s\_\-]*$/', // Must start with a letter
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.role.index')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($this->isProtectedRole($role->name)) {
            return redirect()
                ->route('admin.role.index')
                ->with('error', 'Cannot modify system role: ' . $role->name);
        }

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('roles', 'name')->ignore($role->id),
                'not_in:' . $this->getProtectedRolesForValidation(),
                'regex:/^[a-zA-Z][a-zA-Z0-9\s\_\-]*$/', // Must start with a letter
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.role.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        if ($this->isProtectedRole($role->name)) {
            return redirect()
                ->route('admin.role.index')
                ->with('error', 'Cannot delete system role: ' . $role->name);
        }

        $role->delete();

        return redirect()->route('admin.role.index')->with('success', 'Role deleted successfully.');
    }
}
