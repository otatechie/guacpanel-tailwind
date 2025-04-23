<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserAccountController extends Controller
{
    private const PASSWORD_RULES = [
        'required',
        'confirmed',
        'min:8',
        'regex:/[A-Z]/',
        'regex:/[0-9]/',
        'regex:/[^A-Za-z0-9]/',
    ];


    private function getAuthUser(): User
    {
        return Auth::user();
    }


    public function index()
    {
        $user = $this->getAuthUser();

        abort_if($user->id !== Auth::id(), 403, 'You are not authorized to access this profile.');

        return Inertia::render('UserAccount/IndexPage', [
            'user' => array_merge($user->only('name', 'email', 'location'), []),
        ]);
    }


    public function indexTwoFactorAuthentication()
    {
        $user = $this->getAuthUser();

        $data = [
            'user' => $user,
            'qrCodeSvg' => $user->two_factor_secret ? $user->twoFactorQrCodeSvg() : null,
            'recoveryCodes' => $user->two_factor_secret ? json_decode(decrypt($user->two_factor_recovery_codes, true)) : null,
        ];

        return Inertia::render('UserAccount/IndexTwoFactorAuthenticationPage', $data);
    }


    public function indexPasswordExpired()
    {
        $user = $this->getAuthUser();

        if (!$user->isPasswordExpired()) {
            return redirect()->route('home');
        }

        return Inertia::render('UserAccount/IndexPagePasswordExpired');
    }


    public function updateExpiredPassword(Request $request)
    {
        $user = $this->getAuthUser();

        $validatedData = $request->validate([
            'password' => self::PASSWORD_RULES
        ]);

        $now = now();
        $user->update([
            'password' => Hash::make($validatedData['password']),
            'password_changed_at' => $now,
            'password_expiry_at' => $now->copy()->addMonths(3),
        ]);

        session()->flash('success', 'Password has been updated successfully.');

        return redirect()->route('home');
    }
}
