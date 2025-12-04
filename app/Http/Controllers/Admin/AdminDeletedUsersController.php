<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataTablePaginationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminDeletedUsersController extends Controller
{
    public function __construct(private DataTablePaginationService $pagination)
    {
        $this->middleware('permission:view-users');
    }

    public function index(Request $request)
    {
        $perPage = $this->pagination->resolvePerPageWithDefaults($request);

        $users = User::query()
            ->onlyDeleted()
            ->with(['roles:id,name', 'permissions:id,name'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id'                            => $user->id,
                    'name'                          => $user->name,
                    'email'                         => $user->email,
                    'email_verified_at'             => $user->email_verified_at,
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
            return redirect()->back()->with('error', 'Super user cannot be deleted.');
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

        $users = User::onlyDeleted()
                    ->forceDelete();

        return redirect()->route('admin.user.index')->with('success', 'All deleted users permanently destroyed.');
    }
}
