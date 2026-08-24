<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personalisation;
use App\Traits\PersonalisationsHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminPersonalisationController extends Controller implements HasMiddleware
{
    use PersonalisationsHelper;

    public static function middleware(): array
    {
        return [new Middleware('permission:view-personalisation|manage-personalisation')];
    }

    public function index(Request $request)
    {
        $personalisation = $this->getPersonalisations();

        return Inertia::render('Admin/Personalisation/IndexPage', [
            'personalisation' => $personalisation,
            'canUpdate' => $request->user()->canAny(['update-personalisation', 'manage-personalisation']),
            'canUpload' => $request->user()->canAny(['upload-personalisation-files', 'manage-personalisation']),
            'canDeleteFiles' => $request->user()->canAny(['delete-personalisation-files', 'manage-personalisation']),
        ]);
    }

    public function updateInfo(Request $request)
    {
        abort_unless($request->user()->canAny(['update-personalisation', 'manage-personalisation']), 403);

        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:100'],
            'copyright_text' => ['nullable', 'string', 'max:50'],
        ]);

        $personalisation = Personalisation::firstOrCreate();

        $personalisation->update($validated);

        return redirect()->back()->with('success', __('notifications.admin.settings_updated_successfully'));
    }

    public function upload(Request $request)
    {
        abort_unless($request->user()->canAny(['upload-personalisation-files', 'manage-personalisation']), 403);

        $request->validate([
            'app_logo' => ['nullable', 'image', 'max:2048'],
            'app_logo_dark' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:2048'],
        ]);

        if ($request->hasFile('app_logo') || $request->hasFile('app_logo_dark') || $request->hasFile('favicon')) {
            $field = $request->hasFile('app_logo')
                ? 'app_logo'
                : ($request->hasFile('app_logo_dark')
                    ? 'app_logo_dark'
                    : 'favicon');

            $file = $request->file($field);
            $fileName = time() . '_' . Str::random(16) . '.' . $file->guessExtension();

            $path = $request->file($field)->storeAs('personalisation', $fileName, 'public');

            $personalisation = Personalisation::firstOrCreate();

            if ($personalisation->$field) {
                if (Storage::disk('public')->exists($personalisation->$field)) {
                    Storage::disk('public')->delete($personalisation->$field);
                }
            }

            $personalisation->update([$field => $path]);

            return response()->json(['path' => Storage::url($path)]);
        }

        return response()->json(['error' => __('notifications.errors.no_file_uploaded')], 400);
    }

    public function delete(Request $request)
    {
        abort_unless($request->user()->canAny(['delete-personalisation-files', 'manage-personalisation']), 403);

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
