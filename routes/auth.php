<?php

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\User\UserAccountController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

//Socialite Authentication Routes
Route::get('/auth/social/{provider}', [SocialiteController::class, 'getSocialRedirect'])->name('social.redirect');
Route::get('/auth/social/{provider}/callback', [SocialiteController::class, 'handleSocialCallback'])->name('social.callback');

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

// Override Verification route so we can add in success toast message.
if (config('guacpanel.email_verification_enabled') && Features::enabled(Features::emailVerification())) {
    Route::middleware(['verify.auth', 'signed', 'throttle:6,1'])
        ->get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->name('verification.verify');
}

// Account Re-activaction and Restore Routes
Route::controller(UserAccountController::class)->prefix('uac')->name('uac.')->group(function () {
    if (config('guacpanel.user.account.deactivate_enabled')) {
        // Route::get('reactivate/{token}', 'reactivateAccount')->name('reactivate'); // TODO :: HERE
    }

    if (config('guacpanel.user.account.restore_enabled')) {
        Route::get('restore/{token}', 'restoreAccount')->name('restore');
    }
});
