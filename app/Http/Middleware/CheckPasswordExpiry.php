<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiry
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isPasswordExpired() && Setting::first()->password_expiry) {
                session()->flash('warning', 'Your password has expired. Please change it to continue.');
                return redirect()->route('user.password.expired');
            }
        }

        return $next($request);
    }
}
