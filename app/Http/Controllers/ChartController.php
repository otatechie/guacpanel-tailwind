<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialMetric;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index()
    {
        $metrics = FinancialMetric::selectRaw("DATE_FORMAT(date, '%b') as month, type, SUM(amount) as total")
            ->whereYear('date', now()->year)
            ->groupBy('month', 'type')
            ->orderBy(DB::raw("STR_TO_DATE(month, '%b')"))
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $income = array_fill_keys($months, 0);
        $expense = array_fill_keys($months, 0);

        foreach ($metrics as $metric) {
            if ($metric->type === 'income') {
                $income[$metric->month] = (float) $metric->total;
            } else {
                $expense[$metric->month] = (float) $metric->total;
            }
        }


        return Inertia::render('Chart', [
            'financialMetrics' => [
                'months' => $months,
                'income' => $income,
                'expense' => $expense
            ],
        ]);
    }
}
