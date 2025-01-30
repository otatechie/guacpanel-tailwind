<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string',  Rule::unique(Permission::class)],
        ]);

        Permission::create($validatedData);

        session()->flash('success', 'Permission created successfully.');
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id, Permission $permission)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string',  Rule::unique('permissions', 'name')->ignore($permission->id)],
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
