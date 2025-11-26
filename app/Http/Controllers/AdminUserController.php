<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DataTablePaginationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function __construct(private DataTablePaginationService $pagination)
    {
        $this->middleware('permission:view-users');
    }

    public function index(Request $request)
    {
        $perPage = $this->pagination->resolvePerPageWithDefaults($request);

        $users = User::query()
            ->with(['roles', 'permissions'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id'                    => $user->id,
                    'name'                  => $user->name,
                    'email'                 => $user->email,
                    'email_verified_at'     => $user->email_verified_at,
                    'disable_account'       => $user->disable_account,
                    'force_password_change' => $user->force_password_change,
                    'created_at'            => $user->created_at,
                    'updated_at'            => $user->updated_at,
                    'roles'                 => $user->roles,
                    'permissions'           => $user->permissions,
                    'is_superuser'          => $user->isSuperUser(),
                ];
            });

        return Inertia::render('Admin/User/IndexUserPage', [
            'users' => $users,
            'roles' => [
                'data' => Role::select(['id', 'name'])->get(),
            ],
            'filters' => $this->pagination->buildFilters($request),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'role'                  => ['nullable', 'exists:roles,id'],
            'force_password_change' => ['boolean'],
        ]);

        $user = User::create($validatedData);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', 'New user account created successfully.');
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit-users');

        $user = User::with(['permissions', 'roles'])->findOrFail($id);

        return Inertia::render('Admin/User/EditUserPage', [
            'user' => [
                'id'                    => $user->id,
                'name'                  => $user->name,
                'email'                 => $user->email,
                'disable_account'       => $user->disable_account,
                'force_password_change' => $user->force_password_change,
                'roles'                 => $user->roles,
                'permissions'           => $user->permissions->map(fn ($permission) => [
                    'id'   => $permission->id,
                    'name' => $permission->name,
                ]),
                'is_superuser' => $user->isSuperUser(),
            ],
            'permissions' => [
                'data' => Permission::select(['id', 'name'])->get(),
            ],
            'roles' => [
                'data' => Role::select(['id', 'name'])->get(),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit-users');

        $user = User::findOrFail($id);

        if ($user->isSuperUser()) {
            $currentRoleId = $user->roles->first()?->id;
            $isRoleBeingChanged = $request->role != $currentRoleId;

            if ($request->disable_account || $request->force_password_change || $isRoleBeingChanged) {
                return redirect()->back()->with('error', 'Superuser account status and role cannot be modified.');
            }
        }

        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', Rule::unique('users')->ignore($id)],
            'role'                  => ['nullable', 'exists:roles,id'],
            'permissions'           => ['sometimes', 'array'],
            'permissions.*'         => ['exists:permissions,id'],
            'disable_account'       => ['boolean'],
            'force_password_change' => ['boolean'],
        ]);

        if ($request->force_password_change && $request->disable_account) {
            return back()->withErrors(['error' => 'User cannot be both disabled and forced to change password.']);
        }

        $user->update([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'force_password_change' => $request->force_password_change,
            'disable_account'       => $request->disable_account,
        ]);

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions ?? []);
        }

        return redirect()->back()->with('success', 'User account updated successfully');
    }

    public function destroy($id)
    {
        $this->authorize('delete-users');

        $user = User::findOrFail($id);

        if (!$user->canBeDeleted()) {
            return redirect()->back()->with('error', 'Superuser cannot be deleted.');
        }

        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');
    }
}
