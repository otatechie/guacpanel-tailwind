<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $settings = Setting::first();
        $twoFactorEnabled = Features::enabled(Features::twoFactorAuthentication());

        if (!$settings || !$settings->two_factor_authentication || !$twoFactorEnabled) {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user->two_factor_secret) {
            session()->flash('warning', __('notifications.account.two_factor_required'));

            return redirect()->route('user.two.factor');
        }

        return $next($request);
    }
}
