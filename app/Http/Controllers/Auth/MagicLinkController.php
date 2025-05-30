<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MagicLoginLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
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

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            session()->flash('error', 'We could not find a user with that email address.');
            return back()->withErrors([
                'email' => 'We could not find a user with that email address.'
            ]);
        }

        $this->sendLoginLink($user, false);

        session()->flash('success', 'We have emailed you a magic link to login!');
        return back();
    }


    protected function sendLoginLink(User $user, bool $isNewUser = false)
    {
        $this->checkPasswordlessEnabled();

        $token = Str::random(40);
        Cache::put("magic_link:{$token}", $user->id, now()->addMinutes(10));

        $url = URL::temporarySignedRoute(
            'magic.login.authenticate',
            now()->addMinutes(10),
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

        $userId = Cache::get("magic_link:{$request->token}");

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'This magic link has expired or is invalid. Please request a new one.');
        }

        $user = User::findOrFail($userId);

        auth()->guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home'))
            ->with('success', 'Welcome back! You have been successfully logged in.');
    }
}
