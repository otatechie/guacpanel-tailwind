<?php

namespace App\Http\Controllers;

use App\Models\FinancialMetric;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ChartController extends Controller
{
    public function index()
    {
        $currentYear = now()->year;

        $metrics = FinancialMetric::select(DB::raw('EXTRACT(MONTH FROM date) as month_number'), 'type', DB::raw('SUM(amount) as total'))
            ->whereYear('date', $currentYear)
            ->groupBy('month_number', 'type')
            ->orderBy('month_number')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $income = array_fill_keys($months, 0);
        $expense = array_fill_keys($months, 0);

        foreach ($metrics as $metric) {
            $monthName = Carbon::create($currentYear, $metric->month_number)->format('M');
            if ($metric->type === 'income') {
                $income[$monthName] = (float) $metric->total;
            } else {
                $expense[$monthName] = (float) $metric->total;
            }
        }

        return Inertia::render('Chart', [
            'financialMetrics' => [
                'months'  => $months,
                'income'  => $income,
                'expense' => $expense,
            ],
        ]);
    }
}
