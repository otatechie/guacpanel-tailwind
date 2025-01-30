<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use OwenIt\Auditing\Models\Audit;

class AdminAuditController extends Controller
{
    public function index()
    {
        $audits = Audit::with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/IndexAuditPage', [
            'audits' => $audits
        ]);
    }
}
