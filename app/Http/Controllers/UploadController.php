<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'app_logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $appLogo = $request->file('app_logo');
        $appLogo->storeAs('app_logo', $appLogo->hashName());

        return response()->json([
            'app_logo' => $appLogo->hashName(),
        ]);
    }
}
