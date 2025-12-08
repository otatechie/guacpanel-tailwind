<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->disable_account) {
            auth()->logout();
            session()->flash('warning', __('notifications.account.disabled', ['email' => config('guacpanel.admin.support_email')]));

            return redirect()->route('login');
        }

        return $next($request);
    }
}
