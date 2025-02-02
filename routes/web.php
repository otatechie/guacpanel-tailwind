<?php

use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\AdminBackupController;
use App\Http\Controllers\AdminPermissionController;
use App\Http\Controllers\AdminPermissionRoleController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserAccountController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::controller(PageController::class)
    ->withoutMiddleware(HandleInertiaRequests::class)
    ->name('public.')
    ->group(function () {
        Route::get('help', 'help')->name('help');
    });

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authenticated Routes
Route::middleware(['web', 'auth'])->group(function () {
    // Password Expired Routes - Must be first and outside password.expired middleware
    Route::get('user/password-expired', [UserAccountController::class, 'indexPasswordExpired'])
        ->name('user.password.expired');
    Route::post('user/password-expired', [UserAccountController::class, 'updateExpiredPassword'])
        ->name('user.password.expired.update');

    // All other authenticated routes with password expiry check
    Route::middleware(['password.expired'])->group(function () {
        // User Account Routes
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('account', [UserAccountController::class, 'index'])->name('index');
            Route::get('two-factor-authentication', [UserAccountController::class, 'twoFactorAuthentication'])->name('two.factor');
        });

        // Admin Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('setting', [AdminSettingController::class, 'index'])->name('setting.index');
            Route::get('audits', [AdminAuditController::class, 'index'])->name('audit');
            Route::get('users', [AdminUserController::class, 'index'])->name('user');
            Route::get('permissions-roles', [AdminPermissionRoleController::class, 'index'])->name('permission.role');
            Route::resource('role', AdminRoleController::class)->except('show');
            Route::resource('permission', AdminPermissionController::class)->except('show');
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

        // Media Library Route
        Route::mediaLibrary();
    });

    // Authentication Routes (outside password.expired but inside auth)
    Route::post('logout', [LogoutController::class, 'destroy'])->name('logout');
});
