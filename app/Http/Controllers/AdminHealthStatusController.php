<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Controller;
use Spatie\Health\Health;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\Commands\RunHealthChecksCommand;

class AdminHealthStatusController extends Controller
{
    public function index(ResultStore $resultStore, Health $health)
    {
        $checkResults = $resultStore->latestResults();
        
        return Inertia::render('Monitoring/IndexPage', [
            'healthChecks' => [
                'lastRanAt' => $checkResults?->finishedAt ? new Carbon($checkResults->finishedAt) : null,
                'results' => $checkResults?->storedCheckResults?->map(function ($result) {
                    return [
                        'label' => $result->label,
                        'status' => $result->status,
                        'notificationMessage' => $result->notificationMessage,
                        'shortSummary' => $result->shortSummary,
                        'meta' => $result->meta,
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
