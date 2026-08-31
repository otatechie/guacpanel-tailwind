<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-notifications']);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo('view-notifications');

    // Distinct ages, so "newest first" has one right answer. `created_at` is not
    // fillable, so it has to be stamped after the insert.
    $this->plain = makePageNotification('Chatty', 'system', 'info', now()->subDays(2));
    $this->readOne = makePageNotification('Shipped', 'release', 'success', now()->subDay());
    $this->dismissedOne = makePageNotification('Broken', 'system', 'error', now());

    AppNotificationRead::create([
        'app_notification_id' => (string) $this->readOne->id,
        'user_id' => (string) $this->user->id,
        'read_at' => now(),
    ]);

    AppNotificationRead::create([
        'app_notification_id' => (string) $this->dismissedOne->id,
        'user_id' => (string) $this->user->id,
        'dismissed_at' => now(),
    ]);
});

function makePageNotification(string $title, string $scope, string $type, $createdAt): AppNotification
{
    $notification = AppNotification::create([
        'scope' => $scope,
        'type' => $type,
        'title' => $title,
        'message' => strtolower($title),
    ]);

    $notification->forceFill(['created_at' => $createdAt])->save();

    return $notification->fresh();
}

function pageTitles(array $query = []): array
{
    $titles = [];

    test()
        ->actingAs(test()->user)
        ->get(route('notifications.index', $query))
        ->assertInertia(function ($page) use (&$titles) {
            $titles = collect($page->toArray()['props']['notifications']['data'])
                ->pluck('title')
                ->all();
        });

    return $titles;
}

test('all means all, dismissed included', function () {
    // It briefly meant "all except dismissed", which is a label that lies:
    // dismissing something made it vanish from the view named All.
    expect(pageTitles())->toBe(['Broken', 'Shipped', 'Chatty']);
});

test('dismissing drops a notification out of unread', function () {
    // This is where the action has to have a visible effect. Broken is dismissed
    // and Shipped is read, so neither belongs here.
    expect(pageTitles(['state' => 'unread']))->toBe(['Chatty']);
});

test('the dismissed state shows only what was dismissed', function () {
    expect(pageTitles(['state' => 'dismissed']))->toBe(['Broken']);
});

test('an unknown state falls back to all rather than erroring', function () {
    expect(pageTitles(['state' => 'nonsense']))->toBe(['Broken', 'Shipped', 'Chatty']);
});

test('the source filter narrows to one scope', function () {
    expect(pageTitles(['scope' => 'release']))->toBe(['Shipped']);
});

test('the table drives sorting through sort_by and sort_dir', function () {
    // These are the parameters Datatable's headers emit, so the page has to
    // speak them rather than its own private sort vocabulary.
    expect(pageTitles(['sort_by' => 'title', 'sort_dir' => 'asc']))->toBe(['Broken', 'Chatty', 'Shipped']);
    expect(pageTitles(['sort_by' => 'title', 'sort_dir' => 'desc']))->toBe(['Shipped', 'Chatty', 'Broken']);
});

test('a sort column outside the whitelist falls back to the date', function () {
    // The ordering reaches an orderByRaw, so nothing else may get that far.
    // Only the column is discarded -- the direction the reader asked for stands.
    $injection = 'id); drop table users --';

    expect(pageTitles(['sort_by' => $injection, 'sort_dir' => 'desc']))->toBe(['Broken', 'Shipped', 'Chatty']);
    expect(pageTitles(['sort_by' => $injection, 'sort_dir' => 'asc']))->toBe(['Chatty', 'Shipped', 'Broken']);
});

test('the filters it echoes back are the ones the table needs to seed itself', function () {
    $this->actingAs($this->user)
        ->get(route('notifications.index'))
        ->assertInertia(
            fn($page) => $page
                ->where('filters.state', 'all')
                ->where('filters.scope', 'all')
                ->where('filters.sort_by', 'created_at')
                ->where('filters.sort_dir', 'desc')
                ->where('filters.per_page', 25),
        );
});
