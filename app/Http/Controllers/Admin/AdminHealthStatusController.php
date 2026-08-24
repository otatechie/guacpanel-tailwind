<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;

class AdminHealthStatusController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:view-health')];
    }

    public function index(ResultStore $resultStore)
    {
        $checkResults = $resultStore->latestResults();

        return Inertia::render('Monitoring/IndexPage', [
            'healthChecks' => [
                'lastRanAt' => $checkResults?->finishedAt
                    ? Carbon::parse($checkResults->finishedAt)->toIso8601String()
                    : null,
                'lastRanAtFormatted' => $checkResults?->finishedAt
                    ? Carbon::parse($checkResults->finishedAt)->diffForHumans()
                    : null,
                'results' =>
                    $checkResults?->storedCheckResults?->map(function ($result) {
                        return [
                            'label' => $result->label,
                            'status' => $result->status,
                            'notificationMessage' => $result->notificationMessage,
                            'shortSummary' => $result->shortSummary,
                            'meta' => collect($result->meta)
                                ->only([
                                    'disk_usage',
                                    'message',
                                    'error',
                                    'used_memory_percentage',
                                    'used_memory',
                                    'database_size',
                                    'table_count',
                                ])
                                ->toArray(),
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
