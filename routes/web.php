<?php

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
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Pages\ChartsController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\TypesenseController;
use App\Http\Controllers\User\BrowserSessionController;
use App\Http\Controllers\User\UserAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/', [PageController::class, 'home'])->name('home');

require __DIR__.'/auth.php';
require __DIR__.'/documentation.php';

// Authenticated Routes
Route::middleware([
    'web',
    'auth',
    'auth.session',
])->group(function () {

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

        // User Account Management Routes
        Route::prefix('user')->name('user.')->group(function () {

            // Force Password Change Routes
            Route::controller(ForcePasswordChangeController::class)->prefix('password')->name('password.')->group(function () {
                Route::get('change', 'edit')->name('change')->withoutMiddleware('force.password.change');
                Route::post('change', 'update')->name('change.update')->withoutMiddleware('force.password.change');
            });

            // 2FA Routes
            Route::get('two-factor-authentication', [UserAccountController::class, 'indexTwoFactorAuthentication'])->name('two.factor');

            // Password Expired Routes
            Route::controller(UserAccountController::class)->group(function () {
                Route::get('password-expired', 'indexPasswordExpired')->name('password.expired')->withoutMiddleware('password.expired');
                Route::post('password-expired', 'updateExpiredPassword')->name('password.expired.update')->withoutMiddleware('password.expired');
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
            Route::prefix('admin')->name('admin.')->group(function () {
                // Settings Routes
                Route::prefix('settings')->name('setting.')->group(function () {
                    Route::controller(AdminSettingController::class)->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/show', 'show')->name('show');
                        Route::post('/update', 'update')->name('update');
                    });
                });

                // User Management Routes
                Route::prefix('users')->name('user.')->group(function () {
                    Route::prefix('verification')->name('verification.')->group(function () {
                        Route::controller(AdminUsersVerificationController::class)->group(function () {
                            Route::post('/toggle/{user}', 'toggle')->name('toggle');
                            Route::post('/send/{user}', 'send')->name('send');
                        });
                    });

                    Route::prefix('deleted')->name('deleted.')->group(function () {
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
                });

                // Audit & History Routes
                Route::get('audits', [AdminAuditController::class, 'index'])->name('audit.index');
                Route::get('login-history', [AdminLoginHistoryController::class, 'index'])->name('login.history.index');
                Route::post('login-history/bulk-destroy', [AdminLoginHistoryController::class, 'bulkDestroy'])->name('login.history.bulk-destroy');

                // Permissions & Roles Routes
                Route::get('permissions/roles', [AdminPermissionRoleController::class, 'index'])->name('permission.role.index');
                Route::resource('roles', AdminRoleController::class)->except('show')->names('role');
                Route::resource('permissions', AdminPermissionController::class)->except('show')->names('permission');

                // Personalization Routes
                Route::prefix('personalization')->name('personalization.')->group(function () {
                    Route::controller(AdminPersonalisationController::class)->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/upload', 'upload')->name('upload');
                        Route::post('/delete', 'delete')->name('delete.file');
                        Route::post('/update-info', 'updateInfo')->name('update.info');
                    });
                });

                // Backup Routes
                Route::prefix('backup')->name('backup.')->group(function () {
                    Route::controller(AdminBackupController::class)->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/create', 'createBackup')->name('create');
                        Route::get('/download/{path}', 'download')->name('download');
                        Route::delete('/{path}', 'destroy')->name('destroy');
                    });
                });

                // Session Management Routes
                Route::prefix('sessions')->name('sessions.')->group(function () {
                    Route::controller(AdminSessionController::class)->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::delete('/{sessionId}', 'destroy')->name('destroy');
                        Route::delete('/user/{userId}', 'destroyAllForUser')->name('destroy-all');
                    });
                });

                // Health Monitoring Routes
                Route::controller(AdminHealthStatusController::class)->prefix('health')->name('health.')->group(function () {
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
