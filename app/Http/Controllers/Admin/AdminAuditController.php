<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataTableFilterService;
use App\Services\DataTablePaginationService;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AdminAuditController extends Controller
{
    public function __construct(
        private DataTablePaginationService $pagination,
        private DataTableFilterService $filter
    ) {
        $this->middleware('permission:view-audits');
    }

    public function index(Request $request)
    {
        $query = Audit::query()
            ->select('id', 'created_at', 'event', 'auditable_type', 'user_type', 'user_id')
            ->with(['user' => function ($q) {
                $q->select('id', 'name');
            }]);

        // Apply search
        $searchableColumns = ['event', 'auditable_type', 'user.name'];
        $query = $this->filter->applySearch($query, $request, $searchableColumns);

        // Apply sorting
        $sortConfig = [
            'event' => [],
            'created_at' => [],
        ];
        $query = $this->filter->applySorting($query, $request, $sortConfig);

        $filteredTotal = $query->count();
        $perPage = $this->pagination->resolvePerPageWithDefaults($request, 'audits', $filteredTotal);

        $audits = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($audit) {
                return [
                    'id'             => $audit->id,
                    'event'          => $audit->event,
                    'auditable_type' => $audit->auditable_type,
                    'user_type'      => $audit->user_type,
                    'user_id'        => $audit->user_id,
                    'created_at'     => $audit->created_at?->toDateTimeString(),
                    'user'           => [
                        'id'   => $audit->user?->id,
                        'name' => $audit->user?->name,
                    ],
                ];
            });

        return inertia('Admin/IndexAuditPage', [
            'audits'  => $audits,
            'filters' => $this->pagination->buildFilters($request),
        ]);
    }
}
