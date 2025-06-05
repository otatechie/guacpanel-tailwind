<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

trait SuperUserProtectionTrait
{
    protected function isSuperUser(User $user): bool
    {
        return $user->hasRole('superuser');
    }
    

    protected function protectSuperUserFromDeletion(User $user): ?RedirectResponse
    {
        if ($this->isSuperUser($user)) {
            return redirect()->back()->with('error', 'Superuser cannot be deleted.');
        }
        return null;
    }


    protected function protectSuperUserRoleChange(User $user): ?RedirectResponse
    {
        if ($this->isSuperUser($user)) {
            return redirect()->back()->with('error', 'Superuser role cannot be changed.');
        }
        return null;
    }


    protected function protectSuperUserAccountChanges(User $user): ?RedirectResponse
    {
        if ($this->isSuperUser($user)) {
            return redirect()->back()->with('error', 'Superuser account settings cannot be modified.');
        }
        return null;
    }


    protected function protectSuperUserAccountStatusChanges(User $user): ?RedirectResponse
    {
        if ($this->isSuperUser($user)) {
            return redirect()->back()->with('error', 'Superuser account status and role cannot be modified.');
        }
        return null;
    }
}