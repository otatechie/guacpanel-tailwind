<?php

namespace App\Http\Controllers;

use App\Models\Personalisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminPersonalisationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-personalisation');
    }

    public function index()
    {
        $personalisation = Personalisation::first();

        return Inertia::render('Admin/Personalisation/IndexPage', [
            'personalisation' => $personalisation,
        ]);
    }

    public function upload(Request $request)
    {
        $this->authorize('upload-personalisation-files');

        $request->validate([
            'app_logo'      => ['nullable', 'image', 'max:2048'],
            'app_logo_dark' => ['nullable', 'image', 'max:2048'],
            'favicon'       => ['nullable', 'file', 'mimes:png,ico', 'max:2048'],
        ]);

        if ($request->hasFile('app_logo') || $request->hasFile('app_logo_dark') || $request->hasFile('favicon')) {
            $field = $request->hasFile('app_logo') ? 'app_logo' : ($request->hasFile('app_logo_dark') ? 'app_logo_dark' : 'favicon');

            $file = $request->file($field);
            $fileName = time().'_'.$file->getClientOriginalName();

            $path = $request->file($field)->storeAs(
                'personalisation',
                $fileName,
                'public'
            );

            $personalisation = Personalisation::firstOrCreate();

            if ($personalisation->$field) {
                if (Storage::disk('public')->exists($personalisation->$field)) {
                    Storage::disk('public')->delete($personalisation->$field);
                }
            }

            $personalisation->update([$field => $path]);

            return response()->json(['path' => Storage::url($path)]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function updateInfo(Request $request)
    {
        $this->authorize('update-personalisation');

        $validated = $request->validate([
            'app_name'       => ['nullable', 'string', 'max:100'],
            'copyright_text' => ['nullable', 'string', 'max:50'],
        ]);

        $personalisation = Personalisation::firstOrCreate();

        $personalisation->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function delete(Request $request)
    {
        $this->authorize('delete-personalisation-files');

        $request->validate([
            'field' => ['required', 'string', 'in:app_logo,app_logo_dark,favicon'],
        ]);

        $field = $request->input('field');
        $personalisation = Personalisation::first();

        if ($personalisation && $personalisation->$field) {
            if (Storage::disk('public')->exists($personalisation->$field)) {
                Storage::disk('public')->delete($personalisation->$field);
            }
            $personalisation->update([$field => null]);
        }

        return response()->json(['success' => true]);
    }
}
