<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $providerConfig = Config::get('services.'.$provider);

        if (empty($providerConfig)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleSocialCallback(Request $request, string $provider)
    {
        if (
            $request->filled('denied') ||   // denied param is present and not empty
            $request->has('error') ||    // any error param present
            !$request->has('code')         // code is missing
        ) {
            return redirect()
                ->route('login')
                ->with('error', 'Unable to login with '.Str::ucfirst($provider).'.');
        }

        $user = Socialite::driver($provider)->user();

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            Auth::login($existingUser);

            return redirect()
                        ->intended($this->redirectSuccessLogin)
                        ->with('success', Str::ucfirst($provider).' Login Successful');
        }

        $newUser = User::create([
            'name'      => $user->getName(),
            'email'     => $user->getEmail(),
            'password'  => bcrypt(Str::random(24)),
        ]);

        Auth::login($newUser);

        return redirect()
                    ->intended('/dashboard')
                    ->with('success', Str::ucfirst($provider).' Registration Successful');
    }
}
