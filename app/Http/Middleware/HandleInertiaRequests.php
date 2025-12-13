<?php

namespace App\Http\Middleware;

use App\Traits\AppNotificationsHelperTrait;
use App\Traits\PersonalisationsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    use AppNotificationsHelperTrait;
    use PersonalisationsHelper;

    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $avatar = $user?->avatar;
        $gravatar = $user?->gravatar;

        if (Str::lower($user?->profile_image_type) == 'gravatar') {
            $avatar = $gravatar;
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'roles'       => $user->roles->pluck('name'),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                    'avatar'      => $avatar,
                    'gravatar'    => $gravatar,
                ] : null,
            ],

            'csrf_token' => csrf_token(),

            'flash' => [
                'message'                            => fn () => $request->session()->get('message'),
                'success'                            => fn () => $request->session()->get('success'),
                'error'                              => fn () => $request->session()->get('error'),
                'status'                             => fn () => $request->session()->get('status'),
                'warning'                            => fn () => $request->session()->get('warning'),
                'info'                               => fn () => $request->session()->get('info'),
                'danger'                             => fn () => $request->session()->get('danger'),
                'all'                                => fn () => $request->session()->get('_flash.old', []),
                'recovery-codes-generated'           => fn () => $request->session()->get('recovery-codes-generated'),
                'two-factor-authentication-enabled'  => fn () => $request->session()->get('two-factor-authentication-enabled'),
                'two-factor-authentication-disabled' => fn () => $request->session()->get('two-factor-authentication-disabled'),
                'verification-link-sent'             => fn () => $request->session()->get('verification-link-sent'),
                'profile-information-updated'        => fn () => $request->session()->get('profile-information-updated'),
            ],

            'settings' => [
                'passwordlessLogin'        => DB::table('settings')->value('passwordless_login') ?? true,
                'emailVerificationEnabled' => config('guacpanel.email_verification_enabled'),
            ],

            'notifications' => fn () => $this->resolveNotifications($request, 25, [
                'dismissed' => 'undismissed',
            ]),
        ]);
    }
}
