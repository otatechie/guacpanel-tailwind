<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view permissions')->only(['index']);
        $this->middleware('permission:edit permissions')->only(['store', 'update']);
        $this->middleware('permission:delete permissions')->only(['destroy']);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'regex:/^[a-z]+(?:-[a-z]+)*$/i', // Hyphens only and letters only
                Rule::unique(Permission::class)
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Permission name must contain only letters and hyphens in format: resource-action (e.g. posts-edit)'
        ]);

        Permission::create($validatedData);
        session()->flash('success', 'Permission created successfully.');
    }


    public function update(Request $request, Permission $permission)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'regex:/^[a-z]+(?:-[a-z]+)*$/i', // Hyphens only and letters only
                Rule::unique('permissions', 'name')->ignore($permission->id)
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Permission name must contain only letters and hyphens in format: resource-action (e.g. posts-edit)'
        ]);

        $permission->update($validatedData);
        session()->flash('success', 'Permission updated successfully.');
    }


    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}
