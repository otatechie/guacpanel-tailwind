<?php

namespace App\Http\Controllers;

use App\Traits\HasProtectedPermission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{
    use HasProtectedPermission;

    public function __construct()
    {
        $this->middleware('permission:manage-permissions');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'regex:/^[a-z]+(?:-[a-z]+)*$/i', // Hyphens only and letters only
                Rule::unique(Permission::class),
                'not_in:'.$this->getProtectedPermissionsForValidation(),
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex'  => 'Permission name must contain only letters and hyphens in format: resource-action (e.g. posts-edit)',
            'name.not_in' => 'Cannot create permission with this name as it is reserved for system use.',
        ]);

        Permission::create($validatedData);
        session()->flash('success', 'Permission created successfully.');
    }

    public function update(Request $request, Permission $permission)
    {
        if ($this->isProtectedPermission($permission->name)) {
            return redirect()->back()
                ->with('error', 'Cannot modify system permission: '.$permission->name);
        }

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'regex:/^[a-z]+(?:-[a-z]+)*$/i', // Hyphens only and letters only
                Rule::unique('permissions', 'name')->ignore($permission->id),
                'not_in:'.$this->getProtectedPermissionsForValidation(),
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex'  => 'Permission name must contain only letters and hyphens in format: resource-action (e.g. posts-edit)',
            'name.not_in' => 'Cannot use this name as it is reserved for system use.',
        ]);

        $permission->update($validatedData);
        session()->flash('success', 'Permission updated successfully.');
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);

        // Prevent deleting protected system permissions
        if ($this->isProtectedPermission($permission->name)) {
            return redirect()->back()
                ->with('error', 'Cannot delete system permission: '.$permission->name);
        }

        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}
