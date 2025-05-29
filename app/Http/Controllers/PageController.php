<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
class PageController extends Controller
{
    public function terms()
    {
        return Inertia::render('Terms');
    }
}
