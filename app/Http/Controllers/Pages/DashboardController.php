<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller as ParentController;
use App\Models\LoginHistory;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends ParentController
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => $this->getStats(),
            'userGrowth' => $this->getUserGrowth(),
        ]);
    }

    private function getStats(): array
    {
        return Cache::remember('dashboard_stats', 60, function () {
            $totalUsers = User::count();
            $activeSessions = Session::distinct('user_id')->count('user_id');
            $loginsToday = LoginHistory::whereDate('login_at', today())
                ->where('login_successful', true)
                ->count();
            $newUsersThisWeek = User::where('created_at', '>=', now()->subDays(7))->count();
            $prevWeekUsers = User::where('created_at', '>=', now()->subDays(14))
                ->where('created_at', '<', now()->subDays(7))
                ->count();

            return [
                'totalUsers' => $totalUsers,
                'activeSessions' => $activeSessions,
                'loginsToday' => $loginsToday,
                'newUsersThisWeek' => $newUsersThisWeek,
                'userGrowth' => $this->growthPercent($newUsersThisWeek, $prevWeekUsers),
            ];
        });
    }

    private function getUserGrowth(): array
    {
        return Cache::remember('dashboard_user_growth', 300, function () {
            return collect(range(5, 0))->map(function ($i) {
                $date = now()->subMonths($i);

                return [
                    'month' => $date->format('M'),
                    'count' => User::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count(),
                ];
            })->values()->toArray();
        });
    }

    private function growthPercent(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
