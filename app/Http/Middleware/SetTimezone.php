<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    public function handle(Request $request, Closure $next)
    {
        $timezone = config('app.timezone'); // Get default timezone from config

        if ($settings = \App\Models\Personalisation::first()) {
            $timezone = $settings->timezone ?? $timezone;
        }

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $next($request);
    }
}
