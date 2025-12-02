<?php

namespace App\Http\Middleware;

use App\Traits\PersonalisationsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    use PersonalisationsHelper;

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
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
                'passwordlessLogin' => DB::table('settings')->value('passwordless_login') ?? true,
            ],
        ]);
    }
}
