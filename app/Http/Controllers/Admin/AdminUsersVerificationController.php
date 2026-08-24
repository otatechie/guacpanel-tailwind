<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminUsersVerificationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:edit-users|manage-users')];
    }

    public function toggle(Request $request, User $user)
    {
        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'User ' . ($user->email_verified_at ? 'Verified' : 'Un-Verified'));
    }

    public function send(Request $request, User $user)
    {
        $user->sendUserEmailVerificationFromAdmin();

        return redirect()
            ->back()
            ->with('success', 'Verification Email Sent to ' . $user->email);
    }
}
