<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;


class AdminRoleController extends Controller
{
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::create($request->validated());

        return redirect()->back()->with('success', 'Role created successfully.');
    }



    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->validated());

        return redirect()->back()->with('success', 'Role updated successfully.');
    }



    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully');
    }
}

