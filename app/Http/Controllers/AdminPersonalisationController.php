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
            'timezones' => $this->getTimezones()
        ]);
    }


    public function upload(Request $request)
    {
        if ($request->hasFile('app_logo') || $request->hasFile('favicon')) {
            $field = $request->hasFile('app_logo') ? 'app_logo' : 'favicon';
            $path = $request->file($field)->store('personalisation', 'public');

            $personalisation = Personalisation::firstOrCreate();

            if ($personalisation->$field) {
                Storage::disk('public')->delete($personalisation->$field);
            }

            $personalisation->update([$field => $path]);

            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_logo' => ['nullable', 'string'],
            'favicon' => ['nullable', 'string'],
            'timezone' => ['required', 'string'],
            'footer_text' => ['nullable', 'string'],
            'copyright_text' => ['nullable', 'string'],
        ]);

        $personalisation = Personalisation::firstOrCreate();

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
