<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    private $guard;

    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    public function toResponse($request)
    {
        if (!config('guacpanel.auto_login_after_register')) {
            $this->guard->logout();
        }

        if (config('guacpanel.email_verification_enabled')) {
            session()->flash('success', __('notifications.register.pw_success_auto_login_disabled_activation_enabled'));
        } else {
            session()->flash('success', __('notifications.register.pw_success_auto_login_disabled_activation_disabled'));
        }

        return $request->wantsJson()
            ? new JsonResponse('', 201)
            : redirect()->route('login');
    }
}
