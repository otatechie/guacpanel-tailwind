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
            return redirect()->route('user.force.password.change');
        }

        return $next($request);
    }
}
