<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    private $redirectSuccessLogin = 'dashboard';

    public function getSocialRedirect(Request $request, string $provider)
    {
        $providerConfig = Config::get('services.' . $provider);

        if (empty($providerConfig)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleSocialCallback(Request $request, string $provider)
    {
        if (
            $request->filled('denied') || // denied param is present and not empty
            $request->has('error') || // any error param present
            !$request->has('code') // code is missing
        ) {
            return redirect()
                ->route('login')
                ->with('error', __('notifications.login.sm_unable_to_login_with') . Str::ucfirst($provider) . '.');
        }

        $user = Socialite::driver($provider)->user();

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            Auth::login($existingUser);

            return redirect()
                ->intended($this->redirectSuccessLogin)
                ->with('success', Str::ucfirst($provider) . __('notifications.login.sm_login_successful'));
        }

        $newUser = User::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => bcrypt(Str::random(24)),
        ]);

        $newUser->markEmailAsVerified();

        $newUser->assignRole(config('seeders.users.regular.role'));
        event(new Registered($newUser));
        event(new Verified($newUser));

        Auth::login($newUser);

        return redirect()
            ->intended('/dashboard')
            ->with('success', Str::ucfirst($provider) . __('notifications.register.sm_registration_successful'));
    }
}
