<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class UserAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_if($user->id !== Auth::id(), 403, 'You are not authorized to access this profile.');

        return Inertia::render('UserAccount/IndexPage', [
            'user' => $user->only('name', 'email', 'location'),
        ]);
    }


    public function indexTwoFactorAuthentication()
    {
        $user = Auth::user();

        $data = [
            'user' => $user,
            'qrCodeSvg' => $user->two_factor_secret ? $user->twoFactorQrCodeSvg() : null,
            'recoveryCodes' => $user->two_factor_secret ? json_decode(decrypt($user->two_factor_recovery_codes, true)) : null,
        ];

        return Inertia::render('UserAccount/IndexTwoFactorAuthenticationPage', $data);
    }


    public function indexPasswordExpired()
    {
        $user = Auth::user();

        if (!$user->isPasswordExpired()) {
            return redirect()->route('home');
        }

        return Inertia::render('UserAccount/IndexPasswordExpiredPage');
    }


    public function updateExpiredPassword(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ]
        ]);

        // Check if new password is the same as current password
        if (Hash::check($validatedData['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password cannot be the same as your current password.'
            ]);
        }

        $now = now();
        $user->update([
            'password' => Hash::make($validatedData['password']),
            'password_changed_at' => $now,
            'password_expiry_at' => $now->copy()->addMonths(3),
        ]);

        session()->flash('success', 'Password has been updated successfully.');

        return redirect()->route('home');
    }


    public function session(Request $request)
    {
        $user = Auth::user();
        $sessions = [];

        if (config('session.driver') === 'database') {
            $sessionRecords = DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get();

            foreach ($sessionRecords as $session) {
                $sessions[] = [
                    'id' => $session->id ?? '',
                    'agent' => $this->formatAgent($session->user_agent ?? ''),
                    'ip' => $session->ip_address ?? '',
                    'lastActive' => $session->last_activity ? Carbon::createFromTimestamp($session->last_activity)->diffForHumans() : '',
                    'isCurrent' => ($session->id ?? '') === $request->session()->getId(),
                ];
            }
        }

        return Inertia::render('UserAccount/IndexSessionPage', [
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }


    protected function formatAgent($userAgent)
    {
        if (empty($userAgent)) {
            return ['device' => 'Unknown', 'browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        return [
            'device' => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
            'platform' => $agent->platform() ?: 'Unknown',
            'browser' => $agent->browser() ?: 'Unknown',
        ];
    }


    public function deactivateAccount()
    {
        $user = Auth::user();
        $user->update(['disable_account' => true]);

        Auth::logout();
        $request = request();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Account has been deactivated successfully.');
    }


    public function deleteAccount()
    {
        $user = Auth::user();
        $user->delete();

        Auth::logout();
        $request = request();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Account has been deleted successfully.');
    }
}
