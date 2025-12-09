<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Fortify;

class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $redirectTo = Fortify::redirects('email-verification') ?? config('fortify.home', '/');

// dd($redirectTo);

// verification.verify
dd('bender');

return redirect()->route('login')->with('info', __('notifications.verify.already'));

// redirect()->route('verification.verify')->with('info', __('notifications.verify.already'));



return redirect()
    ->intended($redirectTo)
    ->with('info', __('notifications.verify.already'));


        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->intended($redirectTo)
                ->with('info', __('notifications.verify.already'));
        }


dd($redirectTo);

        $request->fulfill();

        return redirect()
            ->intended($redirectTo)
            ->with('success', __('notifications.verify.success'));
    }
}
