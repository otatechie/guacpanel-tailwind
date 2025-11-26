<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $settings = Setting::first();

            if ($settings && $settings->password_expiry && $user->isPasswordExpired()) {
                if (!$request->routeIs('user.password.expired') && !$request->routeIs('user.password.expired.update')) {
                    session()->flash('warning', 'Your password has expired. Please change it to continue.');

                    return redirect()->route('user.password.expired');
                }
            }
        }

        return $next($request);
    }
}
