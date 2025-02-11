<?php

namespace App\Http\Controllers;

use App\Models\Personalisation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AdminPersonalisationController extends Controller
{
    public function index()
    {
        $personalisation = Personalisation::first();

        return Inertia::render('Admin/Personalisation/IndexPage', [
            'personalisation' => $personalisation,
            'timezones' => $this->getTimezones(),
        ]);
    }


    public function upload(Request $request)
    {
        $request->validate([
            'app_logo' => ['required_without:favicon', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'favicon' => ['required_without:app_logo', 'file', 'mimes:ico,png', 'max:1024'],
        ]);

        if ($request->hasFile('app_logo') || $request->hasFile('favicon')) {
            $field = $request->hasFile('app_logo') ? 'app_logo' : 'favicon';

            $file = $request->file($field);
            $sanitizedName = preg_replace('/[^a-zA-Z0-9.]/', '_', $file->getClientOriginalName());

            $path = $request->file($field)->storeAs(
                'personalisation',
                time() . '_' . $sanitizedName,
                'public'
            );

            $personalisation = Personalisation::firstOrCreate();

            if ($personalisation->$field) {
                if (Storage::disk('public')->exists($personalisation->$field)) {
                    Storage::disk('public')->delete($personalisation->$field);
                }
            }

            $personalisation->update([$field => $path]);

            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_logo' => ['nullable', 'string', 'regex:/^personalisation\/[a-zA-Z0-9_\-\.]+$/'],
            'app_name' => ['nullable', 'string', 'max:100'],
            'favicon' => ['nullable', 'string', 'regex:/^personalisation\/[a-zA-Z0-9_\-\.]+$/'],
            'timezone' => ['required', 'string', 'in:' . implode(',', timezone_identifiers_list())],
            'footer_text' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9\s\.,\'"\-&]+$/'],
            'copyright_text' => ['nullable', 'string', 'max:50'],
            'email_notifications' => ['required', 'boolean'],
            'push_notifications' => ['required', 'boolean'],
        ]);

        $personalisation = Personalisation::firstOrCreate([
            'timezone' => config('app.timezone')
        ]);

        foreach (['app_logo', 'favicon'] as $field) {
            if (isset($validated[$field]) && $validated[$field] === null && $personalisation->$field) {
                Storage::disk('public')->delete($personalisation->$field);
            }
        }

        $personalisation->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }


    private function getTimezones()
    {
        $timezones = timezone_identifiers_list();
        $formatted = [];

        foreach ($timezones as $timezone) {
            $formatted[] = [
                'value' => $timezone,
                'label' => str_replace('_', ' ', $timezone)
            ];
        }

        return $formatted;
    }
}
