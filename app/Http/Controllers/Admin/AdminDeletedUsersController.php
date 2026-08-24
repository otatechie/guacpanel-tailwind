<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AdminDeletedUsersController extends Controller implements HasMiddleware
{
    public function __construct(private DataTableService $dataTable) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:view-users|manage-users')];
    }

    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: User::query()
                ->onlyDeleted()
                ->with(['roles:id,name', 'permissions:id,name']),
            request: $request,
            config: [
                'searchable' => ['name', 'email', 'roles.name'],
                'sortable' => [
                    'name' => ['type' => 'simple'],
                    'email' => ['type' => 'simple'],
                    'deleted_at' => ['type' => 'simple'],
                ],
                'resource' => 'deleted_users',
                'transform' => function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'email_verified_at_formatted' => $user->email_verified_at_formatted,
                        'email_verified_at_full' => $user->email_verified_at_full,
                        'password_expiry_at' => $user->password_expiry_at,
                        'password_changed_at' => $user->password_changed_at,
                        'disable_account' => $user->disable_account,
                        'force_password_change' => $user->force_password_change,
                        'created_at_full' => $user->created_at_full,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                        'deleted_at' => $user->deleted_at,
                        'created_at_formatted' => $user->created_at_formatted,
                        'deleted_at_formatted' => $user->deleted_at_formatted,
                        'deleted_at_full' => $user->deleted_at_full,
                        'auto_destroy' => $user->auto_destroy,
                        'auto_destroy_date' => $user->auto_destroy_date,
                        'auto_destroy_date_formatted' => $user->auto_destroy_date_formatted,
                        'auto_destroy_date_full' => $user->auto_destroy_date_full,
                        'restore_date' => $user->restore_date,
                        'restore_date_formatted' => $user->restore_date_formatted,
                        'restore_date_full' => $user->restore_date_full,
                        'roles' => $user->roles,
                        'permissions' => $user->permissions,
                        'is_superuser' => $user->isSuperUser(),
                    ];
                },
            ],
        );

        return Inertia::render('Admin/User/IndexDeletedUsersPage', [
            'users' => $result['data'],
            'roles' => [
                'data' => Role::select(['id', 'name'])->get(),
            ],
            'filters' => $result['filters'],
        ]);
    }

    public function restore(Request $request, $id)
    {
        abort_unless($request->user()->canAny(['edit-users', 'manage-users']), 403);

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $deletedUsersCount = User::query()->onlyDeleted()->count();
        $msg = $user->name . '\'s account restored successfully';

        if ($deletedUsersCount > 0) {
            return redirect()->back()->with('success', $msg);
        }

        return redirect()->route('admin.user.index')->with('success', $msg);
    }

    public function destroy(Request $request, $id)
    {
        abort_unless($request->user()->canAny(['delete-users', 'manage-users']), 403);

        $user = User::onlyTrashed()->findOrFail($id);

        if (!$user->canBeDeleted()) {
            return redirect()->back()->with('error', __('notifications.errors.su_cannot_be_deleted'));
        }

        $user->forceDelete();

        $deletedUsersCount = User::query()->onlyDeleted()->count();
        $msg = $user->name . '\'s account permanently destroyed successfully';

        if ($deletedUsersCount > 0) {
            return redirect()->back()->with('success', $msg);
        }

        return redirect()->route('admin.user.index')->with('success', $msg);
    }

    public function destroyAll(Request $request)
    {
        abort_unless($request->user()->canAny(['delete-users', 'manage-users']), 403);

        $validatedData = $request->validate([
            'confirm_destroy_all' => ['accepted', 'boolean'],
        ]);

        User::onlyDeleted()
            ->get()
            ->each(function ($user) {
                if ($user->canBeDeleted()) {
                    $user->forceDelete();
                }
            });

        return redirect()
            ->route('admin.user.index')
            ->with('success', __('notifications.admin.all_users_deleted_successfully'));
    }
}
