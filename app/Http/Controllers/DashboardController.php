<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\FinancialMetric;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'financialMetrics' => $this->getFinancialMetrics(),
            'stats' => $this->getStats(),
        ]);
    }
    

    public function getFinancialMetrics()
    {
        return Cache::remember('financial_metrics', 300, function () {
            $metrics = FinancialMetric::selectRaw("DATE_FORMAT(date, '%b') as month, type, SUM(amount) as total")
                ->whereYear('date', now()->year)
                ->groupBy('month', 'type')
                ->orderBy(DB::raw("STR_TO_DATE(month, '%b')"))
                ->get();

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            
            $income = array_fill_keys($months, 0);
            $expense = array_fill_keys($months, 0);

            foreach ($metrics as $metric) {
                if ($metric->type === 'income') {
                    $income[$metric->month] = (float) $metric->total;
                } else {
                    $expense[$metric->month] = (float) $metric->total;
                }
            }

            return [
                'months' => $months,
                'income' => $income,
                'expense' => $expense
            ];
        });
    }


    public function getStats()
    {
        return Cache::remember('dashboard_stats', 60, function () {
            $totalMembers = User::count();
            $newMembersToday = User::whereDate('created_at', today())->count();
            $memberGrowth = $this->calculateGrowthPercentage(
                User::whereDate('created_at', '>=', now()->subDays(7))->count(),
                User::whereDate('created_at', '>=', now()->subDays(14))->whereDate('created_at', '<', now()->subDays(7))->count()
            );

            $totalIncome = FinancialMetric::where('type', 'income')
                ->whereYear('date', now()->year)
                ->whereMonth('date', now()->month)
                ->sum('amount');

            $lastMonthIncome = FinancialMetric::where('type', 'income')
                ->whereYear('date', now()->subMonth()->year)
                ->whereMonth('date', now()->subMonth()->month)
                ->sum('amount');

            $incomeGrowth = $this->calculateGrowthPercentage($totalIncome, $lastMonthIncome);

            $totalExpense = FinancialMetric::where('type', 'expense')
                ->whereYear('date', now()->year)
                ->whereMonth('date', now()->month)
                ->sum('amount');

            $lastMonthExpense = FinancialMetric::where('type', 'expense')
                ->whereYear('date', now()->subMonth()->year)
                ->whereMonth('date', now()->subMonth()->month)
                ->sum('amount');

            $expenseGrowth = $this->calculateGrowthPercentage($totalExpense, $lastMonthExpense);

            return [
                [
                    'title' => 'Total Members',
                    'value' => number_format($totalMembers),
                    'growth' => sprintf('%+.1f%%', $memberGrowth),
                    'details' => [
                        'new_today' => $newMembersToday,
                        'total' => $totalMembers
                    ]
                ],
                [
                    'title' => 'Monthly Income',
                    'value' => '$' . number_format($totalIncome, 2),
                    'growth' => sprintf('%+.1f%%', $incomeGrowth),
                    'details' => [
                        'current' => $totalIncome,
                        'previous' => $lastMonthIncome
                    ]
                ],
                [
                    'title' => 'Monthly Expenses',
                    'value' => '$' . number_format($totalExpense, 2),
                    'growth' => sprintf('%+.1f%%', $expenseGrowth),
                    'details' => [
                        'current' => $totalExpense,
                        'previous' => $lastMonthExpense
                    ]
                ],
                [
                    'title' => 'Net Income',
                    'value' => '$' . number_format($totalIncome - $totalExpense, 2),
                    'growth' => sprintf('%+.1f%%', $this->calculateGrowthPercentage(
                        $totalIncome - $totalExpense,
                        $lastMonthIncome - $lastMonthExpense
                    )),
                    'details' => [
                        'current' => $totalIncome - $totalExpense,
                        'previous' => $lastMonthIncome - $lastMonthExpense
                    ]
                ]
            ];
        });
    }


    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }


    public function refreshStats()
    {
        Cache::forget('dashboard_stats');
        return response()->json($this->getStats());
    }
    

    public function refreshFinancialMetrics()
    {
        Cache::forget('financial_metrics');
        return response()->json($this->getFinancialMetrics());
    }
}
