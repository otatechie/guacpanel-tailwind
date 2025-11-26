<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $settings = Setting::first() ?? new Setting();

        return Inertia::render('Admin/IndexManageSettingPage', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'password_expiry'           => ['boolean'],
            'passwordless_login'        => ['boolean'],
            'two_factor_authentication' => ['boolean'],
        ]);

        $validatedData['two_factor_authentication'] = false;

        Setting::updateOrCreate([], $validatedData);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
