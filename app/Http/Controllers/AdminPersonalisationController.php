<?php

namespace App\Http\Controllers;

use App\Models\Personalisation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminPersonalisationController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Personalisation/IndexPage');
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['accepted', 'string', 'max:255'],
            'app_logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'favicon' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'footer_text' => ['accepted', 'string', 'max:255'],
            'copyright_text' => ['accepted', 'string', 'max:255'],
            'email_notifications' => ['accepted', 'boolean'],
            'push_notifications' => ['accepted', 'boolean'],
            'timezone' => ['accepted', 'string', 'max:255'],
            'date_format' => ['accepted', 'string', 'max:255'],
            'time_format' => ['accepted', 'string', 'max:255'],
        ]);

        $personalisation = Personalisation::first();
    }
}
