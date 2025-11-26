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
            session()->flash('warning', 'Account disabled. Email <a href="mailto:support@example.com" class="underline hover:text-orange-800">support@example.com</a> for help.');

            return redirect()->route('login');
        }

        return $next($request);
    }
}
