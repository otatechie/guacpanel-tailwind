<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->force_password_change) {
            session()->flash('warning', __('notifications.account.force_pw_change'));

            return redirect()->route('user.password.change');
        }

        return $next($request);
    }
}
