<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSocialiteProviders
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providers = collect(config('services', []))
                            ->filter(fn ($provider) => $provider['enabled'] ?? false)
                            ->toArray();

        $providerConfig = config('socialite');
        $providerConfig['providers'] = $providers;

        $request->attributes->set('providersConfig', $providerConfig);

        return $next($request);
    }
}
