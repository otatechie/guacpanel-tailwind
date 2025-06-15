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
        $loginHistory = LoginHistory::with('user')
            ->select([
                'id',
                'user_id',
                'user_type',
                'user_agent',
                'login_at',
                'login_successful'
            ])
            ->latest('login_at')
            ->paginate($request->input('per_page', 10))
            ->through(function ($item) {
                $agent = new Agent();
                $agent->setUserAgent($item->user_agent);

                $item->login_at_diff = $item->login_at?->diffForHumans();
                $item->logout_at_diff = $item->logout_at?->diffForHumans();
                $item->device_info = [
                    'device' => $agent->device() ?: 'Unknown',
                    'platform' => $agent->platform() ?: 'Unknown',
                    'browser' => $agent->browser() ?: 'Unknown',
                ];

                $item->status = [
                    'success' => $item->login_successful ?? true,
                ];

                $item->username = $item->user?->name ?? 'Unknown User';

                return $item;
            });

        return Inertia::render('Admin/IndexLoginHistoryPage', [
            'loginHistory' => $loginHistory
        ]);
    }
}
