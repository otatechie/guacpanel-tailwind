<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Fortify;

class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $redirectTo = Fortify::redirects('email-verification') ?? config('fortify.home', '/');

        if (config('guacpanel.auth_required_to_verify_email')) {
            return $this->handleAuthRequiredFlow($request, $redirectTo);
        }

        return $this->handleAuthNotRequiredFlow($request, $redirectTo);
    }

    protected function handleAuthRequiredFlow(EmailVerificationRequest $request, string $redirectTo): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->intended($redirectTo)
                ->with('info', __('notifications.verify.already'));
        }

        $request->fulfill();

        return redirect()
            ->intended($redirectTo)
            ->with('success', __('notifications.verify.success'));
    }

    protected function handleAuthNotRequiredFlow(EmailVerificationRequest $request, string $redirectTo): RedirectResponse
    {
        $isAuthenticated = auth()->check();

        $routeName = $redirectTo === '/dashboard' ? 'dashboard' : 'login';
        if (! $isAuthenticated) {
            $routeName = 'login';
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->route($routeName)
                ->with('info', __('notifications.verify.already'));
        }

        $request->fulfill();

        if ($isAuthenticated) {
            return redirect()
                ->route('dashboard')
                ->with('success', __('notifications.verify.success'));
        }

        return redirect()
            ->route('login')
            ->with('success', __('notifications.verify.success'));
    }
}
