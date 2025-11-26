<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $settings = Setting::first();
        if (!$settings || !$settings->two_factor_authentication) {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user->two_factor_secret) {
            session()->flash('warning', 'Two-factor authentication is required. Please enable it to continue.');

            return redirect()->route('user.two.factor');
        }

        return $next($request);
    }
}
