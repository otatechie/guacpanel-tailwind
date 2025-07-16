<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TypesenseController;
use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\AdminBackupController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\AdminSessionController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\BrowserSessionController;
use App\Http\Controllers\AdminPermissionController;
use App\Http\Controllers\AdminHealthStatusController;
use App\Http\Controllers\AdminLoginHistoryController;
use App\Http\Controllers\AdminSystemNoticeController;
use App\Http\Controllers\AdminPermissionRoleController;
use App\Http\Controllers\ForcePasswordChangeController;
use App\Http\Controllers\AdminPersonalisationController;

Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/', [PageController::class, 'home'])->name('home');

// Authenticated Routes
Route::middleware(['web', 'auth', 'auth.session'])->group(function () {
    // Logout Route
    Route::post('logout', [LogoutController::class, 'destroy'])->name('logout');

    Route::middleware(['disable.account', 'force.password.change', 'password.expired'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // User Account Management Routes
        Route::prefix('user')->name('user.')->group(function () {
            // Force Password Change Routes
            Route::controller(ForcePasswordChangeController::class)->group(function () {
                Route::get('password/change', 'edit')
                    ->name('password.change')
                    ->withoutMiddleware('force.password.change');
                Route::post('password/change', 'update')
                    ->name('password.change.update')
                    ->withoutMiddleware('force.password.change');
            });

            // 2FA Routes
            Route::get('two-factor-authentication', [UserAccountController::class, 'indexTwoFactorAuthentication'])
                ->name('two.factor');

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
            Route::controller(UserAccountController::class)->group(function () {
                Route::get('account', 'index')->name('index');
                Route::post('account/deactivate', 'deactivateAccount')->name('deactivate');
                Route::post('account/delete', 'deleteAccount')->name('delete');
            });

            // Browser Session Routes
            Route::controller(BrowserSessionController::class)->group(function () {
                Route::get('account/sessions', 'index')->name('session.index');
                Route::post('account/sessions/logout', 'logoutOtherDevices')->name('session.logout');
                Route::delete('account/sessions/{sessionId}', 'destroySession')->name('session.destroy');
            });
        });

        // Chart Routes
        Route::get('charts', [ChartController::class, 'index'])->name('chart.index');

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

                // System Notice Routes
                Route::controller(AdminSystemNoticeController::class)->prefix('system-notices')->name('system-notice.')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{systemNotice}', 'edit')->name('edit');
                    Route::put('/{systemNotice}', 'update')->name('update');
                    Route::delete('/{systemNotice}', 'destroy')->name('destroy');
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

// Documentation Routes
Route::prefix('documentation')->name('documentation.')->group(function () {
    Route::controller(DocumentationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/installation', 'installation')->name('installation');
        Route::get('/features', 'features')->name('features');
        Route::get('/components', 'components')->name('components');
    });
});

// Magic Link Authentication Routes
Route::middleware(['guest', 'web'])->group(function () {
    Route::prefix('magic')->name('magic.')->group(function () {
        Route::controller(MagicLinkController::class)->group(function () {
            Route::get('/register', 'create')->name('create');
            Route::post('/register', 'store')->name('store');
            Route::post('/login', 'login')->name('login');
            Route::get('/{token}', 'authenticate')->name('login.authenticate');
        });
    });
});
