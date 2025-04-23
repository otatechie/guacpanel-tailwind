<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use Inertia\Inertia;

class PageController extends Controller
{
    public function indexFlight()
    {
        return Inertia::render('IndexFlight', []);
    }
}
