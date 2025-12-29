<?php

use App\Events\AppNotificationRequested;
use App\Http\Controllers\Admin\AdminAppNotificationsController;
use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminBackupController;
use App\Http\Controllers\Admin\AdminDeletedUsersController;
use App\Http\Controllers\Admin\AdminHealthStatusController;
use App\Http\Controllers\Admin\AdminLoginHistoryController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Controllers\Admin\AdminPermissionRoleController;
use App\Http\Controllers\Admin\AdminPersonalisationController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminUsersVerificationController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Notifications\AppNotificationController;
use App\Http\Controllers\Notifications\AppNotificationPageController;
use App\Http\Controllers\Pages\ChartsController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\TypesenseController;
use App\Http\Controllers\User\BrowserSessionController;
use App\Http\Controllers\User\UserAccountController;
use App\Jobs\CleanupDeletedAppNotificationsJob;
use App\Mail\NotificationsCleanupReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/terms', [PageController::class, 'terms'])->name('terms');

require __DIR__ . '/auth.php';

// Authenticated Routes
Route::middleware(['web', 'auth', 'auth.session'])->group(function () {
    // Logout Route
    Route::post('logout', [LogoutController::class, 'destroy'])->name('logout');

    Route::middleware([
        'account.locked',
        'account.disabled',
        'account.verified',
        'force.password.change',
        'password.expired',
    ])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Notification
        if (config('guacpanel.notifications.enabled')) {
            // Notifications (Page)
            Route::get('/notifications/all', [AppNotificationPageController::class, 'index'])
                ->name('notifications.index')
                ->middleware('permission:view-notifications|manage-notifications');

            // Notifications (API).
            Route::prefix('notifications')->group(function () {
                // List payload used by dropdown + page reconciliation
                Route::get('/', [AppNotificationController::class, 'index'])->middleware(
                    'permission:view-notifications|manage-notifications',
                );

                // Edit actions
                Route::post('/read-all', [AppNotificationController::class, 'markAllRead'])->middleware(
                    'permission:edit-notifications|manage-notifications',
                );

                Route::post('/dismiss-all', [AppNotificationController::class, 'dismissAll'])->middleware(
                    'permission:edit-notifications|manage-notifications',
                );

                Route::post('/{notification}/read', [AppNotificationController::class, 'markRead'])->middleware(
                    'permission:edit-notifications|manage-notifications',
                );

                Route::post('/{notification}/unread', [AppNotificationController::class, 'markUnread'])->middleware(
                    'permission:edit-notifications|manage-notifications',
                );

                Route::post('/{notification}/dismiss', [AppNotificationController::class, 'dismiss'])->middleware(
                    'permission:edit-notifications|manage-notifications',
                );

                Route::post('/{notification}/undismiss', [AppNotificationController::class, 'undismiss'])->middleware(
                    'permission:edit-notifications|manage-notifications',
                );

                // Bulk (edit OR delete OR manage)
                Route::post('/bulk', [AppNotificationController::class, 'bulk'])->middleware(
                    'permission:edit-notifications|delete-notifications|manage-notifications',
                );

                // Expire (manage)
                Route::post('/expire', [AppNotificationController::class, 'expire'])->middleware(
                    'permission:manage-notifications',
                );

                // Delete (delete OR manage)
                Route::delete('/{notification}', [AppNotificationController::class, 'destroy'])->middleware(
                    'permission:delete-notifications|manage-notifications',
                );
            });
        }

        // User Account Management Routes
        Route::prefix('user')
            ->name('user.')
            ->group(function () {
                // Force Password Change Routes
                Route::controller(ForcePasswordChangeController::class)
                    ->prefix('password')
                    ->name('password.')
                    ->group(function () {
                        Route::get('change', 'edit')->name('change')->withoutMiddleware('force.password.change');
                        Route::post('change', 'update')
                            ->name('change.update')
                            ->withoutMiddleware('force.password.change');
                    });

                // 2FA Routes
                Route::get('two-factor-authentication', [
                    UserAccountController::class,
                    'indexTwoFactorAuthentication',
                ])->name('two.factor');

                // Password Expired Routes
                Route::controller(UserAccountController::class)->group(function () {
                    Route::get('password-expired', 'indexPasswordExpired')
                        ->name('password.expired')
                        ->withoutMiddleware('password.expired');
                    Route::post('password-expired', 'updateExpiredPassword')
                        ->name('password.expired.update')
                        ->withoutMiddleware('password.expired');
                });

                // User Account Routes
                Route::prefix('account')->group(function () {
                    Route::controller(UserAccountController::class)->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('deactivate', 'deactivateAccount')->name('deactivate');
                        Route::post('delete', 'deleteAccount')->name('delete');
                    });

                    // Browser Session Routes
                    Route::controller(BrowserSessionController::class)->group(function () {
                        Route::get('sessions', 'index')->name('session.index');
                        Route::post('sessions/logout', 'logoutOtherDevices')->name('session.logout');
                        Route::delete('sessions/{sessionId}', 'destroySession')->name('session.destroy');
                    });
                });
            });

        // Chart Routes
        Route::get('charts', [ChartsController::class, 'index'])->name('chart.index');

        // Protected Routes requiring 2FA
        Route::middleware(['require.two.factor'])->group(function () {
            // Admin Routes
            Route::prefix('admin')
                ->name('admin.')
                ->group(function () {
                    // Settings Routes
                    Route::prefix('settings')
                        ->name('setting.')
                        ->group(function () {
                            Route::controller(AdminSettingController::class)->group(function () {
                                Route::get('/', 'index')->name('index');
                                Route::get('/show', 'show')->name('show');
                                Route::post('/update', 'update')->name('update');
                            });
                        });

                    // User Management Routes
                    Route::prefix('users')
                        ->name('user.')
                        ->group(function () {
                            Route::prefix('verification')
                                ->name('verification.')
                                ->group(function () {
                                    Route::controller(AdminUsersVerificationController::class)->group(function () {
                                        Route::post('/toggle/{user}', 'toggle')->name('toggle');
                                        Route::post('/send/{user}', 'send')->name('send');
                                    });
                                });

                            Route::prefix('deleted')
                                ->name('deleted.')
                                ->group(function () {
                                    Route::controller(AdminDeletedUsersController::class)->group(function () {
                                        Route::get('/', 'index')->name('index');
                                        Route::post('/destroy-all', 'destroyAll')->name('destroy-all');
                                        Route::post('/{id}', 'restore')->name('restore');
                                        Route::delete('/{id}', 'destroy')->name('destroy');
                                    });
                                });

                            Route::controller(AdminUserController::class)->group(function () {
                                Route::get('/', 'index')->name('index');
                                Route::get('/create', 'create')->name('create');
                                Route::post('/', 'store')->name('store');
                                Route::get('/{id}', 'edit')->name('edit');
                                Route::put('/{id}', 'update')->name('update');
                                Route::delete('/{id}', 'destroy')->name('destroy');
                            });

                            // Impersonation Routes
                            Route::prefix('impersonate')
                                ->name('impersonate.')
                                ->controller(ImpersonationController::class)
                                ->group(function () {
                                    Route::post('/stop', 'stop')->name('stop');
                                    Route::post('/{user}', 'start')
                                        ->name('start')
                                        ->middleware('permission:impersonate-users');
                                });
                        });

                    // Audit & History Routes
                    Route::get('audits', [AdminAuditController::class, 'index'])->name('audit.index');
                    Route::get('login-history', [AdminLoginHistoryController::class, 'index'])->name(
                        'login.history.index',
                    );
                    Route::post('login-history/bulk-destroy', [
                        AdminLoginHistoryController::class,
                        'bulkDestroy',
                    ])->name('login.history.bulk-destroy');

                    // Admin Notifications
                    if (config('guacpanel.notifications.enabled')) {
                        Route::prefix('notifications')
                            ->name('notifications.')
                            ->group(function () {
                                Route::get('/', [AdminAppNotificationsController::class, 'index'])->name('index');
                                Route::get('/create', [AdminAppNotificationsController::class, 'create'])->name(
                                    'create',
                                );
                                Route::post('/', [AdminAppNotificationsController::class, 'store'])->name('store');
                                Route::post('/bulk-destroy', [
                                    AdminAppNotificationsController::class,
                                    'bulkDestroy',
                                ])->name('bulk-destroy');
                                Route::get('/deleted', [AdminAppNotificationsController::class, 'deleted'])->name(
                                    'deleted.index',
                                );
                                Route::get('/{id}/edit', [AdminAppNotificationsController::class, 'edit'])->name(
                                    'edit',
                                );
                                Route::put('/{id}', [AdminAppNotificationsController::class, 'update'])->name('update');
                                Route::delete('/{id}', [AdminAppNotificationsController::class, 'destroy'])->name(
                                    'destroy',
                                );
                            });
                    }

                    // Permissions & Roles Routes
                    Route::get('permissions/roles', [AdminPermissionRoleController::class, 'index'])->name(
                        'permission.role.index',
                    );
                    Route::resource('roles', AdminRoleController::class)->except('show')->names('role');
                    Route::resource('permissions', AdminPermissionController::class)
                        ->except('show')
                        ->names('permission');

                    // Personalization Routes
                    Route::prefix('personalization')
                        ->name('personalization.')
                        ->group(function () {
                            Route::controller(AdminPersonalisationController::class)->group(function () {
                                Route::get('/', 'index')->name('index');
                                Route::post('/upload', 'upload')->name('upload');
                                Route::post('/delete', 'delete')->name('delete.file');
                                Route::post('/update-info', 'updateInfo')->name('update.info');
                            });
                        });

                    // Backup Routes
                    Route::prefix('backup')
                        ->name('backup.')
                        ->group(function () {
                            Route::controller(AdminBackupController::class)->group(function () {
                                Route::get('/', 'index')->name('index');
                                Route::post('/create', 'createBackup')->name('create');
                                Route::get('/download/{path}', 'download')->name('download');
                                Route::delete('/{path}', 'destroy')->name('destroy');
                            });
                        });

                    // Session Management Routes
                    Route::prefix('sessions')
                        ->name('sessions.')
                        ->group(function () {
                            Route::controller(AdminSessionController::class)->group(function () {
                                Route::get('/', 'index')->name('index');
                                Route::delete('/{sessionId}', 'destroy')->name('destroy');
                                Route::delete('/user/{userId}', 'destroyAllForUser')->name('destroy-all');
                            });
                        });

                    // Health Monitoring Routes
                    Route::controller(AdminHealthStatusController::class)
                        ->prefix('health')
                        ->name('health.')
                        ->group(function () {
                            Route::get('/', 'index')->name('index');
                            Route::post('refresh', 'runHealthChecks')->name('refresh');
                        });
                });
        });

        // Dashboard API endpoints
        Route::prefix('api/dashboard')->group(function () {
            Route::get('/financial-metrics', [DashboardController::class, 'refreshFinancialMetrics']);
        });

        // Typesense routes
        Route::middleware(['auth', 'throttle:60,1'])->group(function () {
            Route::get('/typesense/scoped-key', [TypesenseController::class, 'getScopedKey']);
            Route::post('/typesense/multi-search', [TypesenseController::class, 'multiSearch']);
        });
    });
});

// Temp Testing Routes
Route::prefix('_test')
    ->middleware(['auth', 'ensure-local-testing'])
    ->group(function () {
        Route::get('/', function () {
            return 'Hello Tester!';
        });

        Route::post('notify/user', function () {
            event(
                new AppNotificationRequested(
                    userId: (string) auth()->id(),
                    message: 'User notification test (DB + Broadcast).',
                    data: [],
                    scope: 'user',
                    type: 'success',
                    title: 'Test: User',
                ),
            );

            return response()->noContent();
        });

        Route::post('notify/system', function () {
            event(
                new AppNotificationRequested(
                    userId: null,
                    message: 'System notification test (DB + Broadcast).',
                    data: [],
                    scope: 'system',
                    type: 'info',
                    title: 'Test: System',
                ),
            );

            return response()->noContent();
        });

        Route::post('notify/release', function () {
            event(
                new AppNotificationRequested(
                    userId: null,
                    message: 'Release notification test (DB + Broadcast).',
                    data: [],
                    scope: 'release',
                    type: 'info',
                    title: 'Test: Release',
                ),
            );

            return response()->noContent();
        });

        Route::post('notifications/cleanup-email', function () {
            $to = (string) config('guacpanel.notifications.auto_clean_send_email_to', '');
            abort_if($to === '', 422, 'Missing config guacpanel.notifications.auto_clean_send_email_to');

            $days = (int) config('guacpanel.notifications.auto_cleanup_deleted_days', 30);
            $days = max(1, $days);
            $cutoff = now()->subDays($days);

            $deleted = 0;

            if (request()->boolean('run_cleanup')) {
                $deleted = (int) dispatch_sync(new CleanupDeletedAppNotificationsJob());
            } else {
                Mail::to($to)->send(new NotificationsCleanupReport(deleted: 0, cutoff: $cutoff, days: $days));
            }

            return response()->json([
                'ok' => true,
                'sent_to' => $to,
                'deleted' => $deleted,
                'cutoff' => $cutoff->toDateTimeString(),
                'days' => $days,
            ]);
        });
    });
