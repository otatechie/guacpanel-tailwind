<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Laravel\Fortify\Rules\Password;

class ForcePasswordChangeController extends Controller
{
    public function edit(Request $request)
    {
        if ($request->user()->force_password_change) {
            return Inertia::render('Auth/ChangePassword', [
                'user' => $request->user()
            ]);
        }

        return redirect()->route('home');
    }


    public function update(Request $request)
    {
        $key = 'user.password.change.update:' . $request->user()->id;
        $maxAttempts = 3;
        $decaySeconds = 120;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts, $decaySeconds)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'password' => "Too many attempts. Please try again in " . ceil($seconds / 60) . " minutes."
            ]);
        }

        RateLimiter::hit($key, $decaySeconds);

        $validatedData = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ]
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validatedData['password']),
            'force_password_change' => false,
        ]);

        RateLimiter::clear($key);
        session()->flash('success', 'Password has been updated successfully.');

        return redirect()->route('home');
    }
}
