<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class AdminLoginHistoryController extends Controller implements HasMiddleware
{
    public function __construct(private DataTableService $dataTable) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:view-login-history|manage-login-history')];
    }

    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: LoginHistory::with('user')
                ->select(['id', 'user_id', 'user_type', 'ip_address', 'user_agent', 'login_at', 'login_successful'])
                ->orderByDesc('login_at'),
            request: $request,
            config: [
                'searchable' => ['user.name', 'ip_address', 'user_agent'],
                'sortable' => [
                    'login_at' => ['type' => 'simple'],
                ],
                'resource' => 'login_history',
                'transform' => function ($item) {
                    $agent = new Agent();
                    $agent->setUserAgent($item->user_agent);

                    $item->login_at_diff = $item->login_at?->diffForHumans();
                    $item->login_at_exact = $item->login_at?->toDayDateTimeString();
                    $item->device_info = [
                        'device' => $agent->device() ?: 'Unknown',
                        'platform' => $agent->platform() ?: 'Unknown',
                        'browser' => $agent->browser() ?: 'Unknown',
                    ];

                    $item->status = [
                        'success' => (bool) $item->login_successful,
                    ];

                    $item->username = $item->user?->name ?? 'Unknown User';

                    return $item;
                },
            ],
        );

        return Inertia::render('Admin/IndexLoginHistoryPage', [
            'loginHistory' => $result['data'],
            'filters' => $result['filters'],
            'canManage' => $request->user()->can('manage-login-history'),
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        abort_unless($request->user()->can('manage-login-history'), 403);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        LoginHistory::whereIn('id', array_values(array_filter($data['ids'])))->delete();

        return redirect()
            ->route('admin.login.history.index')
            ->with('success', __('notifications.admin.login_history_deleted'));
    }
}
