<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Personalisation;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = config('app.timezone');

        if ($settings = Personalisation::first()) {
            $timezone = $settings->timezone ?? $timezone;
        }

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $next($request);
    }
}
