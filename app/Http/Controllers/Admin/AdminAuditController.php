<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AdminAuditController extends Controller
{
    public function __construct(
        private DataTableService $dataTable
    ) {
        $this->middleware('permission:view-audits');
    }


    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: Audit::query()
                ->select('id', 'created_at', 'event', 'auditable_type', 'user_type', 'user_id')
                ->with(['user' => function ($q) {
                    $q->select('id', 'name');
                }]),
            request: $request,
            config: [
                'searchable' => ['event', 'auditable_type', 'user.name'],
                'sortable' => [
                    'event' => ['type' => 'simple'],
                    'created_at' => ['type' => 'simple'],
                ],
                'resource' => 'audits',
                'transform' => function ($audit) {
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
                },
            ]
        );

        return inertia('Admin/IndexAuditPage', [
            'audits'  => $result['data'],
            'filters' => $result['filters'],
        ]);
    }
}
