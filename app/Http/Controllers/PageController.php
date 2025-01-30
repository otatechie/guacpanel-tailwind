<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use Inertia\Inertia;

class PageController extends Controller
{

    public function help()
    {
        return Inertia::render('IndexHelpPage', [
        ]);
    }
}
