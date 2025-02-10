<?php

namespace App\Http\Middleware;

use App\Models\Discussion;
use App\Models\Personalisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Laravolt\Avatar\Avatar;

class HandleInertiaRequests extends Middleware
{
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
        $avatar = new Avatar(config('laravolt.avatar'));
        $personalisation = Personalisation::first() ?? new Personalisation();

        return array_merge(
            parent::share($request),
            [
                'auth' => [
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'username' => $request->user()->username,
                        'roles' => $request->user()->roles->pluck('name'),
                        'avatar' => $avatar
                            ->create($request->user()->name)
                            ->setTheme('pastel')
                            ->setFontSize(48)
                            ->setDimension(100, 100)
                            ->toBase64(),
                    ] : null,
                ],

                'csrf_token' => csrf_token(),

                'flash' => [
                    'message' => fn() => $request->session()->get('message'),
                    'success' => fn() => $request->session()->get('success'),
                    'error' => fn() => $request->session()->get('error'),
                    'status' => fn() => $request->session()->get('status'),
                    'warning' => fn() => $request->session()->get('warning'),
                    'info' => fn() => $request->session()->get('info'),
                    'danger' => fn() => $request->session()->get('danger'),
                ],

                'personalisation' => [
                    'appName' => $personalisation->app_name,
                    'appLogo' => $personalisation->app_logo,
                    'favicon' => $personalisation->favicon,
                    'footerText' => $personalisation->footer_text,
                    'copyrightText' => $personalisation->copyright_text,
                ],
            ],
        );
    }
}
