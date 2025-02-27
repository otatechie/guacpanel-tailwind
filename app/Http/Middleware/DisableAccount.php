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
            session()->flash('warning', 'Your account has been disabled. Please contact support for assistance.');
            return redirect()->route('login');
        }

        return $next($request);
    }
}
