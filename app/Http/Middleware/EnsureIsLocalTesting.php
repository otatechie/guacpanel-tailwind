<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsLocalTesting
{
    public function handle(Request $request, Closure $next): Response
    {
        $abortTo = 404;

        abort_unless(app()->environment([
            'local',
            'testing',
        ]), $abortTo);

        $user = $request->user();
        abort_unless($user, $abortTo);
        // abort_unless($user?->can('manage-notifications'), $abortTo);

        return $next($request);
    }
}
