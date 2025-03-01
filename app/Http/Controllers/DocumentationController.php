<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentationController extends Controller
{
    public function index()
    {
        return Inertia::render('Documentation/IndexDocumentationPage');
    }


    public function intro()
    {
        return Inertia::render('Documentation/IndexIntroPage');
    }


    public function features()
    {
        return Inertia::render('Documentation/IndexFeaturePage');
    }


    public function components()
    {
        return Inertia::render('Documentation/IndexComponentPage');
    }
}
