<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use OwenIt\Auditing\Models\Audit;

class AdminAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-audits');
    }
    
    
    public function index(Request $request)
    {
        return inertia('Admin/IndexAuditPage', [
            'audits' => Audit::query()
                ->select('id', 'created_at', 'event', 'auditable_type', 'user_type', 'user_id')
                ->with(['user' => function($query) {
                    $query->select('id', 'name');
                }])
                ->latest()
                ->paginate($request->input('per_page', 10))
        ]);
    }
}
