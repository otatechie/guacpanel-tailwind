<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\AdminBackupController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\AdminPermissionController;
use App\Http\Controllers\AdminLoginHistoryController;
use App\Http\Controllers\AdminPermissionRoleController;
use App\Http\Controllers\ForcePasswordChangeController;
use App\Http\Controllers\AdminPersonalisationController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;


// Authenticated Routes
Route::middleware(['web', 'auth', 'disable.account', 'force.password.change'])->group(function () {
    // Home Route
    Route::get('/', [HomeController::class, 'index'])->name('home');

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
            Route::get('password-expired', 'indexPasswordExpired')->name('password.expired');
            Route::post('password-expired', 'updateExpiredPassword')->name('password.expired.update');
        });
    });

    // Logout Route
    Route::post('logout', [LogoutController::class, 'destroy'])->name('logout');

    // Protected Routes requiring 2FA
    Route::middleware(['require.two.factor', 'password.expired'])->group(function () {
        // User Account Routes
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('user/account', [UserAccountController::class, 'index'])->name('index');
        });

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
            Route::get('login/history', [AdminLoginHistoryController::class, 'index'])->name('login.history.index');

            // Permissions & Roles Routes
            Route::get('permissions/roles', [AdminPermissionRoleController::class, 'index'])->name('permission.role.index');
            Route::resource('roles', AdminRoleController::class)->except('show')->names('role.index');
            Route::resource('permissions', AdminPermissionController::class)->except('show')->names('permission.index');

            // Personalization Routes
            Route::prefix('personalization')->name('personalization.')->group(function () {
                Route::controller(AdminPersonalisationController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::put('/update', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                    Route::post('/upload', 'upload')->name('upload');
                });
            });
        });

        // Backup Routes
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::controller(AdminBackupController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'createBackup')->name('create');
                Route::get('/download/{path}', 'download')->name('download');
                Route::delete('/{path}', 'destroy')->name('destroy');
            });
        });

        // Health Check Route
        Route::get('health', HealthCheckResultsController::class)->name('health');
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
    Route::prefix('magic-link')->name('magic.')->group(function () {
        Route::controller(MagicLinkController::class)->group(function () {
            Route::get('/register', 'create')->name('register.create');
            Route::post('/register', 'store')->name('register.store');
            Route::post('/login', 'login')->name('login.request');
            Route::get('/{token}', 'authenticate')->name('login.authenticate');
        });
    });
});
