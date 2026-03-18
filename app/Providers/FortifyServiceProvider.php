<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\RegisterResponse;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        $this->configureContracts();
    }

    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    private function configureViews(): void
    {
        Fortify::loginView(function (Request $request) {
            return Inertia::render('Auth/Login', [
                'canResetPassword' => Features::enabled(Features::resetPasswords()),
                'canRegister' => Features::enabled(Features::registration()),
                'status' => $request->session()->get('status'),
                'providersConfig' => $request->attributes->get('providersConfig'),
                'demo' => [
                    'enabled' => config('guacpanel.demo.enabled'),
                    'username' => config('guacpanel.demo.login.username'),
                    'password' => config('guacpanel.demo.login.password'),
                ],
            ]);
        });

        Fortify::resetPasswordView(function ($request) {
            return Inertia::render('Auth/ResetPassword', [
                'token' => $request->route('token'),
                'email' => $request->email,
            ]);
        });

        Fortify::requestPasswordResetLinkView(
            fn(Request $request) => Inertia::render('Auth/ForgotPassword', [
                'status' => $request->session()->get('status'),
            ]),
        );

        Fortify::verifyEmailView(
            fn(Request $request) => Inertia::render('Auth/Verify', [
                'status' => $request->session()->get('status'),
            ]),
        );

        Fortify::registerView(
            fn(Request $request) => Inertia::render('Auth/Register', [
                'providersConfig' => $request->attributes->get('providersConfig'),
            ]),
        );

        Fortify::twoFactorChallengeView(fn() => Inertia::render('Auth/TwoFactorChallenge'));

        Fortify::confirmPasswordView(fn() => Inertia::render('Auth/ConfirmPassword'));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }

    public function configureContracts()
    {
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);

        // Prevent Laravel default auto login after creating account
        if (!config('guacpanel.auto_login_after_register')) {
            app()->singleton(\Laravel\Fortify\Contracts\RegisterResponse::class, RegisterResponse::class);
        }
    }
}
