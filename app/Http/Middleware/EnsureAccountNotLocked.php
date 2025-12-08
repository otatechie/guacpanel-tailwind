<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountNotLocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user, 404);

        if ($user->account_locked) {
            auth()->logout();

            return redirect()->route('login')->with('error', __('notifications.account.locked', ['email' => config('guacpanel.admin.support_email')]));
        }

        return $next($request);
    }
}
