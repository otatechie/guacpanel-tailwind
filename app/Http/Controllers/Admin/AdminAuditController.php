<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OwenIt\Auditing\Models\Audit;

class AdminAuditController extends Controller implements HasMiddleware
{
    public function __construct(private DataTableService $dataTable) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:view-audits')];
    }

    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: Audit::query()
                ->select(
                    'id',
                    'created_at',
                    'event',
                    'auditable_type',
                    'auditable_id',
                    'ip_address',
                    'old_values',
                    'new_values',
                    'user_type',
                    'user_id',
                )
                ->orderByDesc('created_at')
                ->with([
                    'user' => function ($q) {
                        $q->select('id', 'name');
                    },
                ]),
            request: $request,
            config: [
                'searchable' => ['event', 'auditable_type', 'ip_address', 'user.name'],
                'sortable' => [
                    'event' => ['type' => 'simple'],
                    'created_at' => ['type' => 'simple'],
                ],
                'resource' => 'audits',
                'transform' => function ($audit) {
                    $changed = array_keys((array) ($audit->new_values ?: []));

                    return [
                        'id' => $audit->id,
                        'event' => $audit->event,
                        'auditable_type' => $audit->auditable_type,
                        'auditable_id' => $audit->auditable_id,
                        'ip_address' => $audit->ip_address,
                        'changed' => $changed,
                        'user_type' => $audit->user_type,
                        'user_id' => $audit->user_id,
                        'created_at' => $audit->created_at?->toDateTimeString(),
                        'user' => [
                            'id' => $audit->user?->id,
                            'name' => $audit->user?->name,
                        ],
                    ];
                },
            ],
        );

        return inertia('Admin/IndexAuditPage', [
            'audits' => $result['data'],
            'filters' => $result['filters'],
        ]);
    }
}
