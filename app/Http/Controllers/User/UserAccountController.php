<?php

namespace App\Http\Controllers\User;

use App\Events\UserDeleted;
use App\Events\UserRestored;
use App\Models\User;
use App\Traits\UserAccountRestoreTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class UserAccountController extends Controller
{
    use UserAccountRestoreTrait;

    public function index(Request $request)
    {
        $user = Auth::user();

        abort_if($user->id !== Auth::id(), 403, 'You are not authorized to access this profile.');

        $data = [
            'user'              => $user,
            'qrCodeSvg'         => $user->two_factor_secret ? $user->twoFactorQrCodeSvg() : null,
            'recoveryCodes'     => $user->two_factor_secret ? json_decode(decrypt($user->two_factor_recovery_codes, true)) : null,
            'profileEnabled'    => Route::has('user-profile-information.update'),
            'twoFactorEnabled'  => Route::has('two-factor.enable'),
            'passwordEnabled'   => Route::has('user-password.update'),
            'sessions'          => $this->getUserSessionsData($user, $request->session()->getId()),
            'deactivateEnabled' => config('guacpanel.user.account.deactivate_enabled'),
            'deleteEnabled'     => config('guacpanel.user.account.delete_enabled'),
        ];

        return Inertia::render('UserAccount/IndexPage', $data);
    }

    public function indexTwoFactorAuthentication()
    {
        $user = Auth::user();

        $data = [
            'user'              => $user,
            'qrCodeSvg'         => $user->two_factor_secret ? $user->twoFactorQrCodeSvg() : null,
            'recoveryCodes'     => $user->two_factor_secret ? json_decode(decrypt($user->two_factor_recovery_codes, true)) : null,
            'twoFactorEnabled'  => Route::has('two-factor.enable'),
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
            ],
        ]);

        if (Hash::check($validatedData['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password cannot be the same as your current password.',
            ]);
        }

        $now = now();
        $user->update([
            'password'            => Hash::make($validatedData['password']),
            'password_changed_at' => $now,
            'password_expiry_at'  => $now->copy()->addMonths(3),
        ]);

        session()->flash('success', 'Password has been updated successfully.');

        return redirect()->route('home');
    }

    public function session(Request $request)
    {
        $user = Auth::user();

        return Inertia::render('UserAccount/IndexSessionPage', [
            'user'     => $user,
            'sessions' => $this->getUserSessionsData($user, $request->session()->getId()),
        ]);
    }

    protected function getUserSessionsData(User $user, $currentSessionId = null)
    {
        $sessions = [];

        if (config('session.driver') === 'database') {
            $sessionRecords = DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get();

            foreach ($sessionRecords as $session) {
                $sessions[] = [
                    'id'         => $session->id ?? '',
                    'agent'      => $this->formatAgent($session->user_agent ?? ''),
                    'ip'         => $session->ip_address ?? '',
                    'lastActive' => $session->last_activity ? Carbon::createFromTimestamp($session->last_activity)->diffForHumans() : '',
                    'isCurrent'  => ($session->id ?? '') === $currentSessionId,
                ];
            }
        }

        return $sessions;
    }

    protected function formatAgent($userAgent)
    {
        if (empty($userAgent)) {
            return ['device' => 'Unknown', 'browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        return [
            'device'   => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
            'platform' => $agent->platform() ?: 'Unknown',
            'browser'  => $agent->browser() ?: 'Unknown',
        ];
    }

    public function deactivateAccount()
    {
        if (!config('guacpanel.user.account.deactivate_enabled')) {
            return redirect()->back()->with('error', 'Feature disabled in the .env file');
        }

        $user = Auth::user();
        $user->update(['disable_account' => true]);

        Auth::logout();
        $request = request();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Account has been deactivated successfully.');
    }

    public function reactivateAccount()
    {
        // TODO :: HERE
    }

    public function deleteAccount()
    {
        if (!config('guacpanel.user.account.delete_enabled')) {
            return redirect()->back()->with('error', __('notifications.general.feature_disabled'));
        }

        $user = Auth::user();
        $url = $this->setupAccountRestore($user);
        $user->delete();
        event(new UserDeleted($user, $url));
        auth()->logout();

        return redirect()->route('home')->with('success', __('notifications.account.deleted_successfully'));
    }

    public function restoreAccount(Request $request)
    {
        $route = auth()->user()?->name ? 'dashboard' : 'login';

        if (!config('guacpanel.user.account.restore_enabled')) {
            return redirect()->route($route)->with('warning', __('notifications.account.invalid_restore_link'));
        }

        if (!$request->hasValidSignature()) {
            $route = auth()->user()?->name ? 'dashboard' : 'login';

            return redirect()->route($route)->with('warning', __('notifications.account.invalid_restore_link'));
        }

        if (auth()->user()) {
            return redirect()->route($route)->with('error', __('notifications.account.already_logged_in', ['username' => auth()->user()?->name]));
        }

        $token = $request->token;
        $user = User::onlyTrashed()->whereRestoreToken($token)->first();

        if (!$user) {
            return redirect()->route($route)->with('warning', __('notifications.account.invalid_restore_link'));
        }

        if ($user->account_locked) {
            return redirect()->route($route)->with('error', __('notifications.account.locked', ['email' => config('guacpanel.admin.support_email')]));
        }

        $user->restore();
        event(new UserRestored($user));
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', __('notifications.account.account_restored'));
    }
}
