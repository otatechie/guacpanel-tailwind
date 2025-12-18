<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class AdminSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-settings');
    }

    public function index()
    {
        return Inertia::render('Admin/IndexSettingPage');
    }

    public function show()
    {
        $systemSettings = Setting::first() ?? new Setting();

        return Inertia::render('Admin/IndexManageSettingPage', [
            'systemSettings'    => $systemSettings,
            'canResetPassword'  => Features::enabled(Features::resetPasswords()),
            'canRegister'       => Features::enabled(Features::registration()),
            'twoFactorEnabled'  => Features::enabled(Features::twoFactorAuthentication()),
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'password_expiry'           => ['boolean'],
            'passwordless_login'        => ['boolean'],
            'two_factor_authentication' => ['boolean'],
        ]);

        if (!config('guacpanel.mfa_enabled')) {
            $validatedData['two_factor_authentication'] = false;
        }

        Setting::updateOrCreate([], $validatedData);

        return redirect()->back()->with('success', __('notifications.admin.settings_updated_successfully'));
    }
}
