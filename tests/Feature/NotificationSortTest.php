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

    // Distinct titles, types and ages, so every ordering has one right answer.
    // `created_at` is not fillable, so it is stamped after the insert -- passing
    // it to create() leaves all three on the same timestamp and every date
    // ordering then comes back in whatever order the database chose.
    $this->beta = makeNotification('Beta', 'system', 'warning', now()->subDays(2));
    $this->alpha = makeNotification('Alpha', 'release', 'error', now()->subDay());
    $this->gamma = makeNotification('Gamma', 'system', 'info', now());
});

function makeNotification(string $title, string $scope, string $type, $createdAt): AppNotification
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

function titlesSortedBy(string $sort): array
{
    return collect(
        test()
            ->getJson('/notifications?sort=' . $sort)
            ->assertOk()
            ->json('data'),
    )
        ->pluck('title')
        ->all();
}

test('the default ordering is newest first', function () {
    $this->actingAs($this->user);

    expect(titlesSortedBy('newest'))->toBe(['Gamma', 'Alpha', 'Beta']);
    expect(titlesSortedBy('oldest'))->toBe(['Beta', 'Alpha', 'Gamma']);
});

test('the column sorts run on the server, over the whole set', function () {
    // These used to be a second, page-local ordering in the browser that could
    // silently disagree with the sort the toolbar had asked the server for.
    $this->actingAs($this->user);

    expect(titlesSortedBy('title_asc'))->toBe(['Alpha', 'Beta', 'Gamma']);
    expect(titlesSortedBy('title_desc'))->toBe(['Gamma', 'Beta', 'Alpha']);
    expect(titlesSortedBy('type_asc'))->toBe(['Alpha', 'Gamma', 'Beta']);
    expect(titlesSortedBy('scope_asc'))->toBe(['Alpha', 'Gamma', 'Beta']);
});

test('unread and undismissed sort first regardless of the database', function () {
    // NULL ordering is not portable across SQLite, MySQL and Postgres, so this
    // sorts on a CASE instead of on the timestamp itself.
    $this->actingAs($this->user);

    // Written straight to the read table: going through the endpoint would
    // broadcast, and this test is about ordering, not delivery.
    AppNotificationRead::create([
        'app_notification_id' => (string) $this->beta->id,
        'user_id' => (string) $this->user->id,
        'read_at' => now(),
    ]);

    expect(titlesSortedBy('read_asc'))->toBe(['Gamma', 'Alpha', 'Beta']);
    expect(titlesSortedBy('read_desc'))->toBe(['Beta', 'Gamma', 'Alpha']);
});

test('a sort outside the whitelist is rejected', function () {
    // The ordering reaches an orderByRaw, so nothing but the listed values may
    // ever get that far.
    $this->actingAs($this->user)
        ->getJson('/notifications?sort=' . urlencode('title); drop table users --'))
        ->assertStatus(422)
        ->assertJsonValidationErrors('sort');
});
