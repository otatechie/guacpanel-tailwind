<?php

namespace App\Http\Controllers;

use App\Models\MobileDevice;
use App\Models\PhoneBrand;
use App\Models\PhoneModel;
use App\Models\PhoneVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class MobileDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('MobileDevice/IndexPage');
    }
}
