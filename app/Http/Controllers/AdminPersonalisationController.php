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
            'personalisation' => $personalisation
        ]);
    }


    public function upload(Request $request)
    {
        if ($request->hasFile('app_logo')) {
            $personalisation = Personalisation::firstOrCreate();

            // Delete existing file if it exists
            if ($personalisation->app_logo) {
                Storage::disk('public')->delete($personalisation->app_logo);
            }

            // Store new file
            $path = $request->file('app_logo')->store('personalisation', 'public');

            // Update path
            $personalisation->update(['app_logo' => $path]);

            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'app_logo' => ['nullable', 'string'],
        ]);

        $personalisation = Personalisation::firstOrCreate();

        // If app_logo is null (image was removed), delete the existing file
        if (isset($validated['app_logo']) && $validated['app_logo'] === null && $personalisation->app_logo) {
            Storage::disk('public')->delete($personalisation->app_logo);
        }

        $personalisation->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
