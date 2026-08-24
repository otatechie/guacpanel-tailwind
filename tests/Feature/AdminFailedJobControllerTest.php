<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-failed-jobs']);
    Permission::firstOrCreate(['name' => 'manage-failed-jobs']);

    $this->viewer = User::factory()->create();
    $this->viewer->givePermissionTo('view-failed-jobs');

    $this->manager = User::factory()->create();
    $this->manager->givePermissionTo('manage-failed-jobs');

    $this->outsider = User::factory()->create();

    $this->failed = fn(string $uuid = 'job-uuid') => DB::table('failed_jobs')->insert([
        'uuid' => $uuid,
        'connection' => 'database',
        'queue' => 'default',
        'payload' => json_encode(['displayName' => 'App\\Jobs\\SendScheduledAppNotificationsJob']),
        'exception' => "RuntimeException: it broke\n#0 /app/first-frame.php(1)",
        'failed_at' => now(),
    ]);
});

test('it denies users without a failed jobs permission', function () {
    $this->actingAs($this->outsider)->get(route('admin.failed-jobs.index'))->assertForbidden();
});

test('view permission sees the list with the job name and first exception line', function () {
    ($this->failed)();

    $this->actingAs($this->viewer)->get(route('admin.failed-jobs.index'))->assertOk()->assertInertia(
        fn($page) => $page
            ->component('Admin/IndexFailedJobPage')
            ->where('failedJobs.data.0.job', 'App\\Jobs\\SendScheduledAppNotificationsJob')
            ->where('failedJobs.data.0.exception', 'RuntimeException: it broke')
            // The stack trace is not in the table; it is fetched on demand.
            ->where('canManage', false),
    );
});

test('view permission cannot retry or delete', function () {
    ($this->failed)();

    $this->actingAs($this->viewer)
        ->post(route('admin.failed-jobs.retry', ['uuid' => 'job-uuid']))
        ->assertForbidden();

    $this->actingAs($this->viewer)
        ->delete(route('admin.failed-jobs.destroy', ['uuid' => 'job-uuid']))
        ->assertForbidden();

    $this->assertDatabaseHas('failed_jobs', ['uuid' => 'job-uuid']);
});

test('manage permission can delete a failed job', function () {
    ($this->failed)();

    $this->actingAs($this->manager)
        ->delete(route('admin.failed-jobs.destroy', ['uuid' => 'job-uuid']))
        ->assertRedirect();

    $this->assertDatabaseMissing('failed_jobs', ['uuid' => 'job-uuid']);
});

test('deleting a job that is not there is a 404, not a silent success', function () {
    $this->actingAs($this->manager)
        ->delete(route('admin.failed-jobs.destroy', ['uuid' => 'no-such-job']))
        ->assertNotFound();
});

test('the detail endpoint returns the whole exception', function () {
    ($this->failed)();

    $this->actingAs($this->viewer)
        ->getJson(route('admin.failed-jobs.show', ['uuid' => 'job-uuid']))
        ->assertOk()
        ->assertJsonPath('uuid', 'job-uuid')
        ->assertJsonFragment(['queue' => 'default'])
        ->assertSee('first-frame.php', false);
});

test('a payload with no displayName still renders a name', function () {
    DB::table('failed_jobs')->insert([
        'uuid' => 'odd-payload',
        'connection' => 'database',
        'queue' => 'default',
        'payload' => json_encode(['job' => 'Illuminate\\Queue\\CallQueuedHandler@call']),
        'exception' => 'Whoops',
        'failed_at' => now(),
    ]);

    $this->actingAs($this->viewer)
        ->get(route('admin.failed-jobs.index'))
        ->assertOk()
        ->assertInertia(
            fn($page) => $page->where('failedJobs.data.0.job', 'Illuminate\\Queue\\CallQueuedHandler@call'),
        );
});
