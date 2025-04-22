<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class AdminRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage roles']);
    }
    
    public function index()
    {
        return Inertia::render('Admin/PermissionRole/IndexPermissionRolePage', [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all()
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
                'not_in:admin,superadmin', // Prevent reserved names
                'regex:/^[a-zA-Z][a-zA-Z0-9\s\_\-]*$/' // Must start with a letter
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role = Role::create(['name' => $validatedData['name']]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.role.index')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('roles', 'name')->ignore($role->id),
                'not_in:admin,superadmin',
                'regex:/^[a-zA-Z][a-zA-Z0-9\s\_\-]*$/' // Must start with a letter
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role->update(['name' => $validatedData['name']]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.role.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.role.index')->with('success', 'Role deleted successfully.');
    }
}
