<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAuthForVerification
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('guacpanel.auth_required_to_verify_email')) {
            if (!auth()->check()) {
                session()->put('url.intended', $request->fullUrl());

                return redirect()->route('login')->with('warning', __('notifications.verify.login'));
            }
        }

        return $next($request);
    }
}
