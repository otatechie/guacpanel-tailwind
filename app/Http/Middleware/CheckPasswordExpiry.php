<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiry
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isPasswordExpired()) {
                return redirect()->route('user.password.expired')
                    ->with('warning', 'Your password has expired. Please change it to continue.');
            }
        }

        return $next($request);
    }
}
