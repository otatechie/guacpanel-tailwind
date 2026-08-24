<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckPasswordExpiry;
use App\Http\Middleware\DisableAccount;
use App\Http\Middleware\EmailVerificationCheck;
use App\Http\Middleware\EnsureAccountNotLocked;
use App\Http\Middleware\EnsureIsLocalTesting;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandleSocialiteProviders;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RequireAuthForVerification;
use App\Http\Middleware\RequireTwoFactor;
use App\Http\Middleware\ShareSystemNotifications;
use App\Http\Middleware\ValidateSignature;
use App\Jobs\CleanupDeletedAppNotificationsJob;
use App\Jobs\DestroySoftDeletedUsersJob;
use App\Jobs\SendScheduledAppNotificationsJob;
use App\Jobs\SoftDeleteExpiredAppNotificationsJob;
use App\Mail\ExceptionOccurred;
use Illuminate\Console\Scheduling\Schedule;
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
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            ShareSystemNotifications::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'auth'                  => Authenticate::class,
            'verify.auth'           => RequireAuthForVerification::class,
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
            'ensure-local-testing'  => EnsureIsLocalTesting::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            if (config('exceptions.emailExceptionEnabled')) {
                try {
                    // Never mail credentials or secrets submitted with the failing request
                    $redacted = ['password', 'password_confirmation', 'current_password', 'token', 'code', 'two_factor_code'];
                    $body = collect(request()?->all() ?? [])
                        ->map(fn($value, $key) => in_array(strtolower((string) $key), $redacted, true) ? '[REDACTED]' : $value)
                        ->all();

                    $content = [
                        'message' => $e->getMessage(),
                        'file'    => $e->getFile(),
                        'line'    => $e->getLine(),
                        'trace'   => $e->getTrace(),
                        'url'     => request()?->url(),
                        'body'    => $body,
                        'ip'      => request()?->ip(),
                    ];
                    Mail::send(new ExceptionOccurred($content));
                } catch (Throwable $ex) {
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
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new DestroySoftDeletedUsersJob())->daily()->withoutOverlapping()->onOneServer();

        // Routes, nav and the command palette all honour this flag; the
        // scheduler did not, so cron kept working a feature nobody could reach.
        if (config('guacpanel.notifications.enabled')) {
            $schedule->job(new CleanupDeletedAppNotificationsJob())->daily()->withoutOverlapping()->onOneServer();
            $schedule->job(new SendScheduledAppNotificationsJob())->everyFifteenMinutes()->withoutOverlapping()->onOneServer();
            $schedule->job(new SoftDeleteExpiredAppNotificationsJob())->daily()->withoutOverlapping()->onOneServer();
        }
    })
    ->withEvents(false) // turn off folder auto scanning, manually define events outide of Laravel's default.
    ->create();
