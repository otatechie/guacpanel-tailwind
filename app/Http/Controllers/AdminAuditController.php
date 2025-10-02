<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use OwenIt\Auditing\Models\Audit;
use App\Services\DataTablePaginationService;

class AdminAuditController extends Controller
{
    public function __construct(private DataTablePaginationService $pagination)
    {
        $this->middleware('permission:view-audits');
    }
    
    
    public function index(Request $request)
    {
        $perPage = $this->pagination->resolvePerPageWithDefaults($request, 'audits');

        $audits = Audit::query()
            ->select('id', 'created_at', 'event', 'auditable_type', 'user_type', 'user_id')
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($audit) {
                return [
                    'id' => $audit->id,
                    'event' => $audit->event,
                    'auditable_type' => $audit->auditable_type,
                    'user_type' => $audit->user_type,
                    'user_id' => $audit->user_id,
                    'created_at' => $audit->created_at?->diffForHumans(),
                    'user' => [
                        'id' => $audit->user?->id,
                        'name' => $audit->user?->name,
                    ],
                ];
            });

        return inertia('Admin/IndexAuditPage', [
            'audits' => $audits,
            'filters' => $this->pagination->buildFilters($request),
        ]);
    }
}
