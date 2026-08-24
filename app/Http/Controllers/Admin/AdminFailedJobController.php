<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FailedJob;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * The queue runs on the database connection and this kit dispatches four
 * scheduled jobs, so a failure lands in failed_jobs and nothing surfaces it.
 * Health checks report that the queue is alive; they say nothing about the jobs
 * that died on it.
 */
class AdminFailedJobController extends Controller implements HasMiddleware
{
    public function __construct(private DataTableService $dataTable) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:view-failed-jobs|manage-failed-jobs')];
    }

    public function index(Request $request)
    {
        $result = $this->dataTable->process(
            query: FailedJob::query()->orderByDesc('failed_at'),
            request: $request,
            config: [
                'searchable' => ['queue', 'exception'],
                'sortable' => [
                    'failed_at' => ['type' => 'simple'],
                    'queue' => ['type' => 'simple'],
                ],
                'resource' => 'failed_jobs',
                'transform' => fn($job) => [
                    'id' => $job->id,
                    'uuid' => $job->uuid,
                    'connection' => $job->connection,
                    'queue' => $job->queue,
                    'job' => $this->jobName($job->payload),
                    'exception' => $this->firstLine($job->exception),
                    'failed_at' => optional($job->failed_at)->diffForHumans(),
                ],
            ],
        );

        return Inertia::render('Admin/IndexFailedJobPage', [
            'failedJobs' => $result['data'],
            'filters' => $result['filters'],
            'canManage' => $request->user()->canAny(['manage-failed-jobs']),
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $job = DB::table('failed_jobs')->where('uuid', $uuid)->first();

        abort_unless($job, 404);

        return response()->json([
            'uuid' => $job->uuid,
            'job' => $this->jobName($job->payload),
            'queue' => $job->queue,
            'failed_at' => $job->failed_at,
            'exception' => $job->exception,
        ]);
    }

    public function retry(Request $request, string $uuid)
    {
        abort_unless($request->user()->can('manage-failed-jobs'), 403);
        abort_unless(DB::table('failed_jobs')->where('uuid', $uuid)->exists(), 404);

        Artisan::call('queue:retry', ['id' => [$uuid]]);

        return redirect()->back()->with('success', __('notifications.admin.failed_job_retried'));
    }

    public function retryAll(Request $request)
    {
        abort_unless($request->user()->can('manage-failed-jobs'), 403);

        Artisan::call('queue:retry', ['id' => ['all']]);

        return redirect()->back()->with('success', __('notifications.admin.failed_jobs_retried'));
    }

    public function destroy(Request $request, string $uuid)
    {
        abort_unless($request->user()->can('manage-failed-jobs'), 403);

        $deleted = DB::table('failed_jobs')->where('uuid', $uuid)->delete();

        abort_unless($deleted, 404);

        return redirect()->back()->with('success', __('notifications.admin.failed_job_deleted'));
    }

    /** The class name out of the serialised payload, without unserialising it. */
    private function jobName(?string $payload): string
    {
        $decoded = json_decode((string) $payload, true);

        return $decoded['displayName'] ?? ($decoded['job'] ?? 'Unknown job');
    }

    /** Exceptions run to hundreds of lines; the table shows the first. */
    private function firstLine(?string $exception): string
    {
        return Str::limit(strtok((string) $exception, "\n") ?: '', 160);
    }
}
