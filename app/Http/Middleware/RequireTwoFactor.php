<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $settings = Setting::first();
        if (!$settings) {
            return $next($request);
        }

        $user = auth()->user();
        if ($settings->two_factor_authentication && !$user->two_factor_secret) {
            session()->flash('warning', 'Two-factor authentication is required. Please set it up to continue.');
            return redirect()->route('user.two.factor');
        }

        return $next($request);
    }
}
