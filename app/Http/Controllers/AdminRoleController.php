<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class AdminRoleController extends Controller
{
    public function store(Request $request)
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

        session()->flash('success', 'Role created successfully.');
    }


    public function update(Request $request, Role $role)
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

        session()->flash('success', 'Role updated successfully.');
    }


    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        session()->flash('success', 'Role deleted successfully.');
    }
}
