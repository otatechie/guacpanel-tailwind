<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckPasswordExpiry;
use App\Http\Middleware\DisableAccount;
use App\Http\Middleware\EmailVerificationCheck;
use App\Http\Middleware\EnsureAccountNotLocked;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandleSocialiteProviders;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RequireTwoFactor;
use App\Http\Middleware\ValidateSignature;
use App\Mail\ExceptionOccurred;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Two\InvalidStateException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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
            'auth'                  => Authenticate::class,
            'guest'                 => RedirectIfAuthenticated::class,
            'signed'                => ValidateSignature::class,
            'password.expired'      => CheckPasswordExpiry::class,
            'require.two.factor'    => RequireTwoFactor::class,
            'account.disabled'      => DisableAccount::class,
            'account.locked'        => EnsureAccountNotLocked::class,
            'account.verified'      => EmailVerificationCheck::class,
            'force.password.change' => ForcePasswordChange::class,
            'role'                  => RoleMiddleware::class,
            'permission'            => PermissionMiddleware::class,
            'role_or_permission'    => RoleOrPermissionMiddleware::class,
            'socialite.providers'   => HandleSocialiteProviders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            if (config('exceptions.emailExceptionEnabled')) {
                try {
                    $content = [
                        'message' => $e->getMessage(),
                        'file'    => $e->getFile(),
                        'line'    => $e->getLine(),
                        'trace'   => $e->getTrace(),
                        'url'     => request()?->url(),
                        'body'    => request()?->all(),
                        'ip'      => request()?->ip(),
                    ];
                    Mail::send(new ExceptionOccurred($content));
                } catch(Throwable $ex) {
                    // Log::error($ex);
                }
            }
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
    ->withEvents(
        discover: [],
        // discover: [
        //     app_path('Listeners'), // keep generic ones
        // ],
    )
    ->create();
