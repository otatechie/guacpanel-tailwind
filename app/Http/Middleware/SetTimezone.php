<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    public function handle(Request $request, Closure $next)
    {
        if ($settings = \App\Models\Personalisation::first()) {
            config(['app.timezone' => $settings->timezone]);
            date_default_timezone_set($settings->timezone);
        }

        return $next($request);
    }
}
