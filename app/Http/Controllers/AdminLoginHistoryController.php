<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class AdminLoginHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-login-history');
    }


    public function index(Request $request)
    {
        $loginHistory = LoginHistory::query()
            ->select([
                'id',
                'user_id',
                'user_agent',
                'login_at',
                'login_successful'
            ])
            ->with(['user' => function($query) {
                $query->select('id', 'name');
            }])
            ->latest('login_at')
            ->paginate($request->input('per_page', 10))
            ->through(function ($item) {
                $agent = new Agent();
                $agent->setUserAgent($item->user_agent);

                return [
                    'id' => $item->id,
                    'login_at_diff' => $item->login_at?->diffForHumans(),
                    'user_agent' => $item->user_agent,
                    'status' => [
                        'success' => $item->login_successful ?? true,
                    ],
                    'device_info' => [
                        'device' => $agent->device() ?: 'Unknown',
                        'platform' => $agent->platform() ?: 'Unknown',
                        'browser' => $agent->browser() ?: 'Unknown',
                    ],
                    'username' => $item->user?->name ?? 'Unknown User',
                ];
            });

        return Inertia::render('Admin/IndexLoginHistoryPage', [
            'loginHistory' => $loginHistory
        ]);
    }


    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:login_history,id']
        ]);

        LoginHistory::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Selected records have been deleted']);
    }
}
