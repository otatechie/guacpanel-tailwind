<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MagicLoginLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MagicLinkController extends Controller
{
    protected function checkPasswordlessEnabled()
    {
        $passwordlessEnabled = Cache::rememberForever('passwordless_login', function () {
            return DB::table('settings')->value('passwordless_login') ?? true;
        });

        if (!$passwordlessEnabled) {
            abort(404, 'Passwordless login is disabled.');
        }
    }

    public function create()
    {
        $this->checkPasswordlessEnabled();

        return Inertia::render('Auth/RegisterMagicLink');
    }

    public function store(Request $request)
    {
        $this->checkPasswordlessEnabled();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        // Rate limiting for registration
        $key = 'magic_link_registration_'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Too many registration attempts. Please try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 300); // 5 minutes

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'email_verified_at' => now(),
        ]);

        $this->sendLoginLink($user, true);
        session()->flash('success', 'Account created! Check your email for the login link.');

        return back();
    }

    public function login(Request $request)
    {
        $this->checkPasswordlessEnabled();

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Rate limiting for login attempts
        $key = 'magic_link_login_'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 60); // 1 minute

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not find a user with that email address.',
            ]);
        }

        $this->sendLoginLink($user, false);
        session()->flash('success', 'We have emailed you a magic link to login!');

        return back();
    }

    protected function sendLoginLink(User $user, bool $isNewUser = false)
    {
        $this->checkPasswordlessEnabled();

        $token = Str::random(64);
        $expiryMinutes = 10;

        // Store token with user ID and expiry
        Cache::put("magic_link:{$token}", [
            'user_id'    => $user->id,
            'created_at' => now()->timestamp,
        ], now()->addMinutes($expiryMinutes));

        $url = URL::temporarySignedRoute(
            'magic.login.authenticate',
            now()->addMinutes($expiryMinutes),
            ['token' => $token]
        );

        Mail::to($user)->send(new MagicLoginLink($url, $isNewUser));
    }

    public function authenticate(Request $request)
    {
        $this->checkPasswordlessEnabled();

        if (!$request->hasValidSignature()) {
            return redirect()->route('login')
                ->with('error', 'This magic link has expired. Please request a new one.');
        }

        $cacheKey = "magic_link:{$request->token}";
        $tokenData = Cache::get($cacheKey);

        if (!$tokenData || !isset($tokenData['user_id'])) {
            return redirect()->route('login')
                ->with('error', 'This magic link has expired or is invalid. Please request a new one.');
        }

        // Check if token is used within 10 minutes
        $createdAt = $tokenData['created_at'] ?? 0;
        if (now()->timestamp - $createdAt > 600) { // 10 minutes in seconds
            Cache::forget($cacheKey);

            return redirect()->route('login')
                ->with('error', 'This magic link has expired. Please request a new one.');
        }

        // Invalidate token after use (one-time use)
        Cache::forget($cacheKey);

        $user = User::findOrFail($tokenData['user_id']);

        auth()->guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home'))
            ->with('success', 'Welcome back! You have been successfully logged in.');
    }
}
