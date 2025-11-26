<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ForcePasswordChangeController extends Controller
{
    public function edit(Request $request)
    {
        if ($request->user()->force_password_change) {
            return Inertia::render('Auth/ChangePassword', [
                'user' => $request->user(),
            ]);
        }

        return redirect()->route('home');
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $key = 'user.password.change.update:'.$user->id;
        $maxAttempts = 3;
        $decaySeconds = 120;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts, $decaySeconds)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'password' => 'Too many attempts. Please try again in '.ceil($seconds / 60).' minutes.',
            ]);
        }

        RateLimiter::hit($key, $decaySeconds);

        $validatedData = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if (Hash::check($validatedData['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password cannot be the same as your current password.',
            ]);
        }

        $user->update([
            'password'              => Hash::make($validatedData['password']),
            'force_password_change' => false,
            'password_changed_at'   => now(),
        ]);

        RateLimiter::clear($key);
        session()->flash('success', 'Password has been updated successfully.');

        return redirect()->route('home');
    }
}
