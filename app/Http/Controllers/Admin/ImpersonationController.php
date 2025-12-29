<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    public function start(Request $request, User $user)
    {
        $impersonator = $request->user();

        if (!$impersonator->hasPermissionTo('impersonate-users')) {
            abort(403, 'You do not have permission to impersonate users.');
        }

        if ($impersonator->id === $user->id) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        if ($user->hasRole('super-admin') && !$impersonator->hasRole('super-admin')) {
            return back()->with('error', 'You cannot impersonate a super admin.');
        }

        // Store the original user ID in the session
        session()->put('impersonator_id', $impersonator->id);
        session()->put('impersonator_name', $impersonator->name);

        // Log the impersonation
        Log::info('User impersonation started', [
            'impersonator_id' => $impersonator->id,
            'impersonator_name' => $impersonator->name,
            'impersonated_id' => $user->id,
            'impersonated_name' => $user->name,
        ]);

        // Log in as the target user
        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('success', "Now viewing as {$user->name}");
    }

    public function stop(Request $request)
    {
        $impersonatorId = session()->get('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard')->with('error', 'You are not impersonating anyone.');
        }

        $impersonator = User::find($impersonatorId);

        if (!$impersonator) {
            session()->forget(['impersonator_id', 'impersonator_name']);
            Auth::logout();
            return redirect()->route('login')->with('error', 'Original user not found.');
        }

        $impersonatedName = $request->user()->name;

        // Log the end of impersonation
        Log::info('User impersonation ended', [
            'impersonator_id' => $impersonator->id,
            'impersonator_name' => $impersonator->name,
            'impersonated_name' => $impersonatedName,
        ]);

        // Clear impersonation session data
        session()->forget(['impersonator_id', 'impersonator_name']);

        // Log back in as the original user
        Auth::login($impersonator);

        return redirect()
            ->route('admin.user.index')
            ->with('success', "Stopped viewing as {$impersonatedName}");
    }
}
