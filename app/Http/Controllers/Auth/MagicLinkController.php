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
        $passwordlessEnabled = DB::table('settings')->value('passwordless_login') ?? true;

        if (!$passwordlessEnabled) {
            abort(404);
        }
    }


    public function create()
    {
        $this->checkPasswordlessEnabled();
        return Inertia::render('Auth/RegisterMagicLink');
    }


    public function register(Request $request)
    {
        $this->checkPasswordlessEnabled();

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
        $this->checkPasswordlessEnabled();

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
        $this->checkPasswordlessEnabled();

        $url = URL::temporarySignedRoute(
            'magic.login',
            now()->addMinutes(10),
            ['token' => $user->id]
        );

        Mail::to($user)->send(new MagicLoginLink($url));
    }


    public function authenticate(Request $request)
    {
        $this->checkPasswordlessEnabled();

        if (!$request->hasValidSignature()) {
            return redirect()->route('login')
                ->with('error', 'This magic link has expired. Please request a new one.');
        }

        $user = User::findOrFail($request->token);
        auth()->guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home'))
            ->with('success', 'Welcome back! You have been successfully logged in.');
    }
}
