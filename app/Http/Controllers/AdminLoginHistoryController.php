<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Services\DataTablePaginationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class AdminLoginHistoryController extends Controller
{
    public function __construct(private DataTablePaginationService $pagination)
    {
        $this->middleware('permission:view-login-history');
    }

    public function index(Request $request)
    {
        $perPage = $this->pagination->resolvePerPageWithDefaults($request);

        $loginHistory = LoginHistory::with('user')
            ->select([
                'id',
                'user_id',
                'user_type',
                'user_agent',
                'login_at',
                'login_successful',
            ])
            ->latest('login_at')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($item) {
                $agent = new Agent();
                $agent->setUserAgent($item->user_agent);

                $item->login_at_diff = $item->login_at?->diffForHumans();
                $item->device_info = [
                    'device'   => $agent->device() ?: 'Unknown',
                    'platform' => $agent->platform() ?: 'Unknown',
                    'browser'  => $agent->browser() ?: 'Unknown',
                ];

                $item->status = [
                    'success' => $item->login_successful ?? true,
                ];

                $item->username = $item->user?->name ?? 'Unknown User';

                return $item;
            });

        return Inertia::render('Admin/IndexLoginHistoryPage', [
            'loginHistory' => $loginHistory,
            'filters'      => $this->pagination->buildFilters($request),
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'exists:login_history,id'],
        ]);

        LoginHistory::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Selected records have been deleted']);
    }
}
