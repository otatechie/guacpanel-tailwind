<?php

namespace App\Services;

use App\Models\FinancialMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialMetricsService
{
    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    /**
     * Income and expense totals per month, keyed by month abbreviation.
     *
     * @return array{months: array<int, string>, income: array<string, float>, expense: array<string, float>}
     */
    public function monthlyTotals(?int $year = null): array
    {
        $year ??= now()->year;

        $metrics = FinancialMetric::select(
            DB::raw('EXTRACT(MONTH FROM date) as month_number'),
            'type',
            DB::raw('SUM(amount) as total'),
        )
            ->whereYear('date', $year)
            ->groupBy('month_number', 'type')
            ->orderBy('month_number')
            ->get();

        $income = array_fill_keys(self::MONTHS, 0);
        $expense = array_fill_keys(self::MONTHS, 0);

        foreach ($metrics as $metric) {
            $monthName = Carbon::create($year, $metric->month_number)->format('M');
            if ($metric->type === 'income') {
                $income[$monthName] = (float) $metric->total;
            } else {
                $expense[$monthName] = (float) $metric->total;
            }
        }

        return [
            'months' => self::MONTHS,
            'income' => $income,
            'expense' => $expense,
        ];
    }
}
