<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller as ParentController;
use App\Services\FinancialMetricsService;
use Inertia\Inertia;

class ChartsController extends ParentController
{
    public function __construct(private FinancialMetricsService $financialMetrics) {}

    public function index()
    {
        return Inertia::render('Charts', [
            'financialMetrics' => $this->financialMetrics->monthlyTotals(),
        ]);
    }
}
