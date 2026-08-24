<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller implements HasMiddleware
{
    public function __construct(private DataTableService $dataTable) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:view-users|manage-users')];
    }

    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: User::query()->with(['roles:id,name']),
            request: $request,
            config: [
                'searchable' => ['name', 'email', 'roles.name'],
                'filterable' => [
                    'status' => [
                        'type' => 'composite',
                        'callback' => fn($query, $value) => match ($value) {
                            'disabled' => $query->where('disable_account', true),
                            'locked' => $query->where('disable_account', false)->where('account_locked', true),
                            'unverified' => $query
                                ->where('disable_account', false)
                                ->where('account_locked', false)
                                ->whereNull('email_verified_at'),
                            'active' => $query
                                ->where('disable_account', false)
                                ->where('account_locked', false)
                                ->whereNotNull('email_verified_at'),
                            default => $query,
                        },
                    ],
                ],
                'sortable' => [
                    'name' => ['type' => 'simple'],
                    'email' => ['type' => 'simple'],
                    'created_at' => ['type' => 'simple'],
                ],
                'resource' => 'users',
                'transform' => function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'disable_account' => $user->disable_account,
                        'account_locked' => $user->account_locked,
                        'created_at_formatted' => $user->created_at_formatted,
                        'roles' => $user->roles,
                        'is_superuser' => $user->isSuperUser(),
                    ];
                },
            ],
        );

        $deletedUsers = User::query()->onlyDeleted()->count();

        return Inertia::render('Admin/User/IndexUserPage', [
            'users' => $result['data'],
            'deletedUsers' => $deletedUsers,
            'roles' => [
                'data' => Role::select(['id', 'name'])->get(),
            ],
            'filters' => $result['filters'],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canAny(['create-users', 'manage-users']), 403);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'exists:roles,id'],
            'force_password_change' => ['boolean'],
        ]);

        $user = User::create($validatedData);

        if (!empty($validatedData['role'])) {
            $user->assignRole($validatedData['role']);
        }

        return redirect()->back()->with('success', __('notifications.admin.new_user_created_successfully'));
    }

    public function edit(Request $request, $id)
    {
        abort_unless($request->user()->canAny(['edit-users', 'manage-users']), 403);

        $user = User::with(['permissions:id,name', 'roles:id,name'])->findOrFail($id);

        return Inertia::render('Admin/User/EditUserPage', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'email_verified_at_formatted' => $user->email_verified_at_formatted,
                'email_verified_at_full' => $user->email_verified_at_full,
                'disable_account' => $user->disable_account,
                'force_password_change' => $user->force_password_change,
                'auto_destroy' => $user->auto_destroy,
                'restore_date_full' => $user->restore_date_full,
                'roles' => $user->roles,
                'permissions' => $user->permissions->map(
                    fn($permission) => [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'description' => $permission->description,
                    ],
                ),
                'is_superuser' => $user->isSuperUser(),
            ],
            'rolePermissionCount' => $user->getPermissionsViaRoles()->count(),
            'roles' => [
                'data' => Role::select(['id', 'name'])->get(),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        abort_unless($request->user()->canAny(['edit-users', 'manage-users']), 403);

        $user = User::findOrFail($id);

        if ($user->isSuperUser()) {
            $currentRoleId = $user->roles->first()?->id;
            $isRoleBeingChanged = $request->has('role') && $request->role != $currentRoleId;

            if ($request->disable_account || $request->force_password_change || $isRoleBeingChanged) {
                return redirect()->back()->with('error', __('notifications.errors.su_status_cannot_be_modified'));
            }
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'role' => ['nullable', 'exists:roles,id'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['exists:permissions,id'],
            'disable_account' => ['boolean'],
            'auto_destroy' => ['boolean'],
            'force_password_change' => ['boolean'],
        ]);

        if ($request->force_password_change && $request->disable_account) {
            return back()->withErrors([
                'error' => __('notifications.errors.user_cannot_be_disabled_and_force_change_pw'),
            ]);
        }

        $attributes = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        foreach (['force_password_change', 'disable_account', 'auto_destroy'] as $flag) {
            if ($request->has($flag)) {
                $attributes[$flag] = $request->boolean($flag);
            }
        }

        $user->update($attributes);

        if ($request->has('role')) {
            $user->syncRoles(array_filter([$request->role]));
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions ?? []);
        }

        return redirect()->back()->with('success', __('notifications.admin.user_account_updated_successfully'));
    }

    public function destroy(Request $request, $id)
    {
        abort_unless($request->user()->canAny(['delete-users', 'manage-users']), 403);

        $user = User::findOrFail($id);

        if (!$user->canBeDeleted()) {
            return redirect()->back()->with('error', __('notifications.errors.su_cannot_be_deleted'));
        }

        $user->delete();

        $previous = url()->previous();
        $cameFromThisUsersEditPage = str_starts_with($previous, route('admin.user.edit', $id));

        return redirect()
            ->to($cameFromThisUsersEditPage ? route('admin.user.index') : $previous)
            ->with('success', __('notifications.admin.user_deleted_successully'));
    }
}
