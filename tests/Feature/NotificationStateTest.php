<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Reverb is not running in tests, and these state changes broadcast.
    config(['broadcasting.default' => 'null']);

    foreach (['view-notifications', 'edit-notifications'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $this->user = User::factory()->create();
    $this->user->givePermissionTo(['view-notifications', 'edit-notifications']);

    $this->notification = AppNotification::create([
        'scope' => 'system',
        'type' => 'info',
        'title' => 'Chatty',
        'message' => 'FYI',
    ]);
});

function stateRow(): ?AppNotificationRead
{
    return AppNotificationRead::where('app_notification_id', (string) test()->notification->id)
        ->where('user_id', (string) test()->user->id)
        ->first();
}

test('marking read persists read_at', function () {
    // These wrote through updateOrCreate(), whose values go through fill() --
    // and read_at was not fillable, so the row was created with a NULL
    // timestamp. The page looked right until it was reloaded.
    $this->actingAs($this->user)
        ->postJson('/notifications/' . $this->notification->id . '/read')
        ->assertSuccessful();

    expect(stateRow()?->read_at)->not->toBeNull();
});

test('marking unread clears read_at again', function () {
    $this->actingAs($this->user)
        ->postJson('/notifications/' . $this->notification->id . '/read')
        ->assertSuccessful();

    $this->actingAs($this->user)
        ->postJson('/notifications/' . $this->notification->id . '/unread')
        ->assertSuccessful();

    expect(stateRow()?->read_at)->toBeNull();
});

test('dismissing persists dismissed_at', function () {
    $this->actingAs($this->user)
        ->postJson('/notifications/' . $this->notification->id . '/dismiss')
        ->assertSuccessful();

    expect(stateRow()?->dismissed_at)->not->toBeNull();
});

test('undismissing clears dismissed_at again', function () {
    $this->actingAs($this->user)
        ->postJson('/notifications/' . $this->notification->id . '/dismiss')
        ->assertSuccessful();

    $this->actingAs($this->user)
        ->postJson('/notifications/' . $this->notification->id . '/undismiss')
        ->assertSuccessful();

    expect(stateRow()?->dismissed_at)->toBeNull();
});

test('the state a user set survives into the listing', function () {
    $this->actingAs($this->user)
        ->postJson('/notifications/' . $this->notification->id . '/read')
        ->assertSuccessful();

    $row = $this->actingAs($this->user)->getJson('/notifications')->assertOk()->json('data.0');

    expect($row['is_read'])->toBeTrue();
});

test('a view-only reader cannot change state', function () {
    // The row menu is gated on this. Every action used to be offered to
    // everyone, and a viewer got a refusal for each -- silently, because the
    // page patched the row on screen and quietly put it back.
    $viewer = User::factory()->create();
    $viewer->givePermissionTo('view-notifications');

    $this->actingAs($viewer)
        ->postJson('/notifications/' . $this->notification->id . '/read')
        ->assertForbidden();

    $this->actingAs($viewer)
        ->postJson('/notifications/' . $this->notification->id . '/dismiss')
        ->assertForbidden();

    expect(stateRow())->toBeNull();
});

test('the listing carries a relative stamp and an exact one', function () {
    // The table shows the relative form and puts the exact time in the title,
    // the way the sessions table does; the export takes the exact one.
    $row = $this->actingAs($this->user)->getJson('/notifications')->assertOk()->json('data.0');

    expect($row['created_at_diff'])->not->toBeNull();
    expect($row['created_at_exact'])->not->toBeNull();
});
