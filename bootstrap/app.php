<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\CheckPasswordExpiry;
use App\Http\Middleware\RequireTwoFactor;
use App\Http\Middleware\DisableAccount;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\HandleSocialiteProviders;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\ValidateSignature;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\InvalidStateException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            // Laravel Passthrough overrides (Optional as of L12+)
            'auth'                  => Authenticate::class,
            'guest'                 => RedirectIfAuthenticated::class,
            'signed'                => ValidateSignature::class,

            // Custom “account” middleware
            'password.expired'      => CheckPasswordExpiry::class,
            'require.two.factor'    => RequireTwoFactor::class,
            'disable.account'       => DisableAccount::class,
            'force.password.change' => ForcePasswordChange::class,

            // Spatie Permission middleware
            'role'                  => RoleMiddleware::class,
            'permission'            => PermissionMiddleware::class,
            'role_or_permission'    => RoleOrPermissionMiddleware::class,

            // Socialite provider switcher
            'socialite.providers'   => HandleSocialiteProviders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            //
        });

        $exceptions->render(function (InvalidStateException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('notifications.errors.sm_session_invalid'),
                ], 422);
            }

            return redirect()
                ->route('login')
                ->with('error', __('notifications.errors.sm_session_invalid'));
        });
    })
    ->create();
