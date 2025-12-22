<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataTableFilterService;
use App\Services\DataTablePaginationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AdminDeletedUsersController extends Controller
{
    public function __construct(
        private DataTablePaginationService $pagination,
        private DataTableFilterService $filter
    ) {
        $this->middleware('permission:view-users');
    }

    public function index(Request $request)
    {
        $query = User::query()
            ->onlyDeleted()
            ->with(['roles:id,name', 'permissions:id,name']);

        // Apply search
        $searchableColumns = ['name', 'email', 'roles.name'];
        $query = $this->filter->applySearch($query, $request, $searchableColumns);

        // Apply sorting
        $sortConfig = [
            'name' => [],
            'email' => [],
            'deleted_at' => [],
        ];
        $query = $this->filter->applySorting($query, $request, $sortConfig);

        $filteredTotal = $query->count();
        $perPage = $this->pagination->resolvePerPageWithDefaults($request, 'deleted_users', $filteredTotal);

        $users = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id'                            => $user->id,
                    'name'                          => $user->name,
                    'email'                         => $user->email,
                    'email_verified_at'             => $user->email_verified_at,
                    'email_verified_at_formatted'   => $user->email_verified_at_formatted,
                    'email_verified_at_full'        => $user->email_verified_at_full,
                    'password_expiry_at'            => $user->password_expiry_at,
                    'password_changed_at'           => $user->password_changed_at,
                    'disable_account'               => $user->disable_account,
                    'force_password_change'         => $user->force_password_change,
                    'created_at_full'               => $user->created_at_full,
                    'created_at'                    => $user->created_at,
                    'updated_at'                    => $user->updated_at,
                    'deleted_at'                    => $user->deleted_at,
                    'created_at_formatted'          => $user->created_at_formatted,
                    'deleted_at_formatted'          => $user->deleted_at_formatted,
                    'deleted_at_full'               => $user->deleted_at_full,
                    'auto_destroy'                  => $user->auto_destroy,
                    'auto_destroy_date'             => $user->auto_destroy_date,
                    'auto_destroy_date_formatted'   => $user->auto_destroy_date_formatted,
                    'auto_destroy_date_full'        => $user->auto_destroy_date_full,
                    'restore_date'                  => $user->restore_date,
                    'restore_date_formatted'        => $user->restore_date_formatted,
                    'restore_date_full'             => $user->restore_date_full,
                    'roles'                         => $user->roles,
                    'permissions'                   => $user->permissions,
                    'is_superuser'                  => $user->isSuperUser(),
                ];
            });

        return Inertia::render('Admin/User/IndexDeletedUsersPage', [
            'users' => $users,
            'roles' => [
                'data' => Role::select(['id', 'name'])->get(),
            ],
            'filters' => $this->pagination->buildFilters($request),
        ]);
    }

    public function restore(Request $request, $id)
    {
        $this->authorize('edit-users');

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
        $this->authorize('delete-users');

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
        $this->authorize('delete-users');

        $validatedData = $request->validate([
            'confirm_destroy_all' => ['accepted', 'boolean'],
        ]);

        $users = User::onlyDeleted()->forceDelete();

        return redirect()->route('admin.user.index')->with('success', __('notifications.admin.all_users_deleted_successfully'));
    }
}
