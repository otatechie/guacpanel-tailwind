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
            $path = $request->file('app_logo')->store('personalisation', 'public');

            // Store the path right after upload
            $personalisation = Personalisation::firstOrCreate();
            $personalisation->update(['app_logo' => $path]);

            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'app_logo' => ['nullable', 'string'], // Uncomment this
        ]);

        $personalisation = Personalisation::firstOrCreate();
        $personalisation->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
