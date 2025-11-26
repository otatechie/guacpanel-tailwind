<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;

class AdminHealthStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-health');
    }

    public function index(ResultStore $resultStore)
    {
        $checkResults = $resultStore->latestResults();

        return Inertia::render('Monitoring/IndexPage', [
            'healthChecks' => [
                'lastRanAt' => $checkResults?->finishedAt ? Carbon::parse($checkResults->finishedAt)->toIso8601String() : null,
                'results'   => $checkResults?->storedCheckResults?->map(function ($result) {
                    return [
                        'label'               => $result->label,
                        'status'              => $result->status,
                        'notificationMessage' => $result->notificationMessage,
                        'shortSummary'        => $result->shortSummary,
                        'meta'                => collect($result->meta)->only([
                            'disk_usage',
                            'message',
                            'error',
                            'used_memory_percentage',
                            'used_memory',
                            'database_size',
                            'table_count',
                        ])->toArray(),
                    ];
                }) ?? [],
            ],
        ]);
    }

    public function runHealthChecks()
    {
        Artisan::call(RunHealthChecksCommand::class);
        session()->flash('success', 'Health checks completed successfully.');

        return redirect()->back();
    }
}
