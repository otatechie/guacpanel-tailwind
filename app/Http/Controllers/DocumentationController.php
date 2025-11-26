<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DocumentationController extends Controller
{
    public function index()
    {
        return Inertia::render('Documentation/IndexDocumentationPage');
    }

    public function installation()
    {
        return Inertia::render('Documentation/IndexInstallPage');
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
