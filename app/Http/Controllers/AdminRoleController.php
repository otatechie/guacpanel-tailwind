<?php

namespace App\Http\Controllers;

use App\Traits\HasProtectedRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;
use App\Traits\HasProtectedPermission;

class AdminRoleController extends Controller
{
    use HasProtectedRoles;


    public function __construct()
    {
        $this->middleware('permission:manage-roles');
    }


    public function index()
    {
        return Inertia::render('Admin/PermissionRole/IndexPermissionRolePage', [
            'roles' => Role::with('permissions:id,name,description')
                ->select('id', 'name', 'description')
                ->get()
                ->map(function ($role) {
                    $role->is_protected = $this->isProtectedRole($role->name);
                    return $role;
                }),
            'permissions' => Permission::select('id', 'name', 'description')->get(),
            'protectedRoles' => $this->getProtectedRoles(),
            'protectedPermissions' => $this->getProtectedPermissions()
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
                'regex:/^[a-zA-Z][a-zA-Z0-9\s\_\-]*$/' // Must start with a letter
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description']
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        session()->flash('success', 'Role created successfully.');
        return redirect()->route('admin.role.index');
    }


    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($this->isProtectedRole($role->name)) {
            session()->flash('error', 'Cannot modify system role: ' . $role->name);
            return redirect()->route('admin.role.index');
        }

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('roles', 'name')->ignore($role->id),
                'not_in:' . $this->getProtectedRolesForValidation(),
                'regex:/^[a-zA-Z][a-zA-Z0-9\s\_\-]*$/' // Must start with a letter
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description']
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        session()->flash('success', 'Role updated successfully.');
        return redirect()->route('admin.role.index');
    }


    public function destroy(string $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        if ($this->isProtectedRole($role->name)) {
            session()->flash('error', 'Cannot delete system role: ' . $role->name);
            return redirect()->route('admin.role.index');
        }

        $role->delete();

        session()->flash('success', 'Role deleted successfully.');
        return redirect()->route('admin.role.index');
    }
}
