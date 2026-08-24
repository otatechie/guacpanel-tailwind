<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class AdminSettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:manage-settings')];
    }

    public function index()
    {
        return Inertia::render('Admin/IndexSettingPage');
    }

    public function show()
    {
        $systemSettings = Setting::first() ?? new Setting();
        $lastAudit = $systemSettings->exists
            ? $systemSettings->audits()->with('user:id,name')->latest()->first()
            : null;

        return Inertia::render('Admin/IndexManageSettingPage', [
            'systemSettings' => $systemSettings,
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'canRegister' => Features::enabled(Features::registration()),
            'twoFactorEnabled' => $this->twoFactorAvailable(),
            'lastChanged' => $lastAudit
                ? [
                    'at' => $lastAudit->created_at->diffForHumans(),
                    'by' => $lastAudit->user?->name,
                ]
                : null,
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'password_expiry' => ['boolean'],
            'passwordless_login' => ['boolean'],
            'two_factor_authentication' => ['boolean'],
        ]);

        if (!$this->twoFactorAvailable()) {
            $validatedData['two_factor_authentication'] = false;
        }

        Setting::updateOrCreate([], $validatedData);

        return redirect()->back()->with('success', __('notifications.admin.settings_updated_successfully'));
    }

    private function twoFactorAvailable(): bool
    {
        return Features::enabled(Features::twoFactorAuthentication());
    }
}
