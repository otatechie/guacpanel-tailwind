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

    // Force Password Change Route
    Route::get('user/change-password', [ForcePasswordChangeController::class, 'edit'])
        ->name('user.force.password.change')->withoutMiddleware('force.password.change');
    Route::post('user/change-password', [ForcePasswordChangeController::class, 'update'])
        ->name('user.force.password.change.update')->withoutMiddleware('force.password.change');

    // 2FA Setup Route
    Route::get('user/two-factor-authentication', [UserAccountController::class, 'twoFactorAuthentication'])
        ->name('user.two.factor');

    // Password Expired Routes
    Route::get('user/password-expired', [UserAccountController::class, 'indexPasswordExpired'])
        ->name('user.password.expired');
    Route::post('user/password-expired', [UserAccountController::class, 'updateExpiredPassword'])
        ->name('user.password.expired.update');

    // Logout Route
    Route::post('logout', [LogoutController::class, 'destroy'])->name('logout');

    // Protected Routes requiring 2FA
    Route::middleware(['require.two.factor'])->group(function () {
        Route::middleware(['password.expired'])->group(function () {
            // User Account Routes
            Route::prefix('user')->name('user.')->group(function () {
                Route::get('account', [UserAccountController::class, 'index'])->name('index');
            });

            // Admin Routes
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::get('setting', [AdminSettingController::class, 'index'])->name('setting.index');
                Route::get('setting/manage', [AdminSettingController::class, 'manageSettings'])->name('setting.manage');
                Route::post('setting/update', [AdminSettingController::class, 'updateSettings'])->name('setting.update');
                Route::get('audits', [AdminAuditController::class, 'index'])->name('audit');
                Route::get('login-history', [AdminLoginHistoryController::class, 'index'])->name('login.history');
                Route::get('users', [AdminUserController::class, 'index'])->name('user');
                Route::get('users/{id}', [AdminUserController::class, 'edit'])->name('user.edit');
                Route::post('/admin/users/{id}', [AdminUserController::class, 'update'])->name('user.update');
                Route::get('permissions-roles', [AdminPermissionRoleController::class, 'index'])->name('permission.role');
                Route::resource('role', AdminRoleController::class)->except('show');
                Route::resource('permission', AdminPermissionController::class)->except('show');
            });

            // Personalisation Routes
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::get('personalisation', [AdminPersonalisationController::class, 'index'])->name('personalisation.index');
                Route::post('personalisation/update', [AdminPersonalisationController::class, 'update'])->name('personalisation.update');
                Route::post('upload', [AdminPersonalisationController::class, 'upload'])->name('upload.store');
            });

            // Backup Routes
            Route::controller(AdminBackupController::class)
                ->prefix('backup')
                ->name('backup.')
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/run', 'runBackup')->name('run');
                    Route::get('/download/{path}', 'downloadBackup')->where('path', '.*')->name('download');
                    Route::delete('/delete/{path}', 'deleteBackup')->where('path', '.*')->name('delete');
                });

            Route::get('health', HealthCheckResultsController::class)->name('health');
        });
    });
});


// Documentation Route
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation');
Route::get('/documentation/intro', [DocumentationController::class, 'intro'])->name('documentation.intro');
Route::get('/documentation/features', [DocumentationController::class, 'features'])->name('documentation.features');


// Magic Link Authentication Routes
Route::middleware(['guest', 'web'])->group(function () {
    Route::controller(MagicLinkController::class)->group(function () {
        Route::get('/magic-link/register', 'create')->name('magic.register.create');
        Route::post('/register/magic-link', 'register')->name('magic.register');
        Route::post('/login/magic-link', 'login')->name('magic.login.request');
        Route::get('/magic-link/{token}', 'authenticate')->name('magic.login');
    });
});
