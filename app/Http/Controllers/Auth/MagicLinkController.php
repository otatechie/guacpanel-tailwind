<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MagicLoginLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MagicLinkController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/RegisterMagicLink');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
        ]);

        $this->sendLoginLink($user);

        return back()->with([
            'success' => 'Account created! Check your email for the login link.'
        ]);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not find a user with that email address.'
            ])->with('error', 'We could not find a user with that email address.');
        }

        $this->sendLoginLink($user);

        return back()->with([
            'success' => 'We have emailed you a magic link to login!'
        ]);
    }


    protected function sendLoginLink(User $user)
    {
        $token = Str::random(32);

        Cache::put(
            'magic_login_' . $token,
            [
                'user_id' => $user->id,
            ],
            now()->addMinutes(10)
        );

        $url = URL::temporarySignedRoute(
            'magic.login',
            now()->addMinutes(10),
            ['token' => $token]
        );

        Mail::to($user)->send(new MagicLoginLink($url));
    }


    public function authenticate(Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                throw new \Exception('This magic link has expired. Please request a new one.');
            }

            $token = $request->token;
            $cacheData = Cache::get('magic_login_' . $token);

            if (!$cacheData) {
                throw new \Exception('This magic link has already been used or has expired. Please request a new one.');
            }

            $user = User::findOrFail($cacheData['user_id']);

            Cache::forget('magic_login_' . $token);

            auth()->guard('web')->login($user);

            return redirect()->intended(config('fortify.home'))
                ->with('success', 'Welcome back! You have been successfully logged in.');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', $e->getMessage());
        }
    }
}
