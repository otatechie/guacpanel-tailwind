<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller as ParentController;
use App\Models\LoginHistory;
use App\Models\Session;
use App\Models\User;
use App\Services\FinancialMetricsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends ParentController
{
    const TREND_DAYS = 14;

    public function __construct(private FinancialMetricsService $financialMetrics) {}

    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => $this->getStats(),
            'userGrowth' => $this->getUserGrowth(),
            'financialMetrics' => $this->getFinancialMetrics(),
            'trends' => $this->getTrends(),
            'usersByRole' => $this->getUsersByRole(),
        ]);
    }

    /**
     * Daily series behind the stat tiles' sparklines. "Active now" has no
     * series: sessions are current state, so there is no history to plot.
     */
    private function getTrends(): array
    {
        return Cache::remember('dashboard_trends', 300, function () {
            $days = collect(range(self::TREND_DAYS - 1, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
            $start = now()
                ->subDays(self::TREND_DAYS - 1)
                ->startOfDay();

            $signUpsByDay = User::selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('created_at', '>=', $start)
                ->groupBy('day')
                ->pluck('total', 'day');

            $loginsByDay = LoginHistory::selectRaw('DATE(login_at) as day, COUNT(*) as total')
                ->where('login_at', '>=', $start)
                ->where('login_successful', true)
                ->groupBy('day')
                ->pluck('total', 'day');

            $signUps = $days->map(fn($day) => (int) ($signUpsByDay[$day] ?? 0));

            $runningTotal = User::where('created_at', '<', $start)->count();
            $totalUsers = $signUps->map(function ($count) use (&$runningTotal) {
                $runningTotal += $count;

                return $runningTotal;
            });

            return [
                'totalUsers' => $totalUsers->all(),
                'loginsToday' => $days->map(fn($day) => (int) ($loginsByDay[$day] ?? 0))->all(),
                'newUsersThisWeek' => $signUps->all(),
            ];
        });
    }

    private function getUsersByRole(): array
    {
        return Cache::remember('dashboard_users_by_role', 300, function () {
            return Role::withCount('users')
                ->orderByDesc('users_count')
                ->get()
                ->map(
                    fn($role) => [
                        'label' => Str::headline($role->name),
                        'value' => $role->users_count,
                    ],
                )
                ->all();
        });
    }

    private function getFinancialMetrics(): array
    {
        return Cache::remember('dashboard_financial_metrics', 300, fn() => $this->financialMetrics->monthlyTotals());
    }

    private function getStats(): array
    {
        return Cache::remember('dashboard_stats', 60, function () {
            $totalUsers = User::count();
            $activeSessions = Session::distinct('user_id')->count('user_id');
            $loginsToday = LoginHistory::whereDate('login_at', today())->where('login_successful', true)->count();
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
            return collect(range(5, 0))
                ->map(function ($i) {
                    $date = now()->subMonths($i);

                    return [
                        'month' => $date->format('M'),
                        'count' => User::whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month)
                            ->count(),
                    ];
                })
                ->values()
                ->toArray();
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
