<?php

namespace App\Http\Controllers;

use App\Traits\HasProtectedPermission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                'regex:/^[a-z]+(?:-[a-z]+)*$/i', 
                Rule::unique(Permission::class),
                'not_in:' . $this->getProtectedPermissionsForValidation(),
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Permission name must contain only letters and hyphens in format: resource-action (e.g. posts-edit)',
            'name.not_in' => 'Cannot create permission with this name as it is reserved for system use.'
        ]);

        Permission::create($validatedData);
        session()->flash('success', 'Permission created successfully.');
        
        return redirect()->back();
    }


    public function update(Request $request, Permission $permission)
    {
        if ($this->isProtectedPermission($permission->name)) {
            session()->flash('error', 'Cannot modify system permission: ' . $permission->name);
            return redirect()->back();
        }

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'regex:/^[a-z]+(?:-[a-z]+)*$/i', // Hyphens only and letters only
                Rule::unique('permissions', 'name')->ignore($permission->id),
                'not_in:' . $this->getProtectedPermissionsForValidation(),
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Permission name must contain only letters and hyphens in format: resource-action (e.g. posts-edit)',
            'name.not_in' => 'Cannot use this name as it is reserved for system use.'
        ]);
        
        $permission->update($validatedData);
        session()->flash('success', 'Permission updated successfully.');
        
        return redirect()->back();
    }


    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);

        // Prevent deleting protected system permissions
        if ($this->isProtectedPermission($permission->name)) {
            session()->flash('error', 'Cannot delete system permission: ' . $permission->name);
            return redirect()->back();
        }

        $permission->delete();
        session()->flash('success', 'Permission deleted successfully.');
        
        return redirect()->back();
    }
}
