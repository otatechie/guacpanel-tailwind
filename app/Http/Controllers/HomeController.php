<?php

namespace App\Http\Controllers;

use App\Models\Beat;

use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {

        return Inertia::render('Home', [

        ]);
    }
}
