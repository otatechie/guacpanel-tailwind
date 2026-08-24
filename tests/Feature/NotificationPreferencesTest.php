<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The feed is gated on view-notifications; without it every read is a 403
    // and every assertion about its contents is vacuously true.
    Permission::firstOrCreate(['name' => 'view-notifications']);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo('view-notifications');
});

test('everything is delivered until a choice is made', function () {
    expect($this->user->notificationPreferences())->toBe(['muted_scopes' => [], 'muted_types' => []]);
});

test('a user can mute announcement scopes and severities', function () {
    $this->actingAs($this->user)
        ->post(route('user.notification.preferences'), [
            'muted_scopes' => ['release'],
            'muted_types' => ['info'],
        ])
        ->assertRedirect();

    expect($this->user->fresh()->notificationPreferences())->toBe([
        'muted_scopes' => ['release'],
        'muted_types' => ['info'],
    ]);
});

test('it rejects scopes that are not mutable', function () {
    // `user` scope is addressed to you personally; silencing it would mean an
    // account nobody can be told about.
    $this->actingAs($this->user)
        ->post(route('user.notification.preferences'), ['muted_scopes' => ['user']])
        ->assertSessionHasErrors('muted_scopes.0');

    $this->actingAs($this->user)
        ->post(route('user.notification.preferences'), ['muted_types' => ['catastrophe']])
        ->assertSessionHasErrors('muted_types.0');
});

test('a muted scope stops reaching the list and the count', function () {
    AppNotification::create([
        'scope' => 'release',
        'type' => 'info',
        'title' => 'Version 4',
        'message' => 'Out now',
    ]);
    AppNotification::create([
        'scope' => 'system',
        'type' => 'warning',
        'title' => 'Maintenance',
        'message' => 'Tonight',
    ]);

    $before = $this->actingAs($this->user)->getJson('/notifications')->assertOk();
    expect($before->json('meta.total_all'))->toBe(2);

    $this->actingAs($this->user)->post(route('user.notification.preferences'), [
        'muted_scopes' => ['release'],
    ]);

    $after = $this->actingAs($this->user->fresh())
        ->getJson('/notifications')
        ->assertOk();

    // The count matters as much as the list: a bell showing 2 over a list of 1
    // is worse than either on its own.
    expect($after->json('meta.total_all'))
        ->toBe(1)
        ->and(collect($after->json('data'))->pluck('title')->all())
        ->toBe(['Maintenance']);
});

test('a muted severity is filtered too', function () {
    AppNotification::create([
        'scope' => 'system',
        'type' => 'info',
        'title' => 'Chatty',
        'message' => 'FYI',
    ]);
    AppNotification::create([
        'scope' => 'system',
        'type' => 'error',
        'title' => 'Broken',
        'message' => 'Act now',
    ]);

    $this->actingAs($this->user)->post(route('user.notification.preferences'), [
        'muted_types' => ['info'],
    ]);

    $response = $this->actingAs($this->user->fresh())
        ->getJson('/notifications')
        ->assertOk();

    expect(collect($response->json('data'))->pluck('title')->all())->toBe(['Broken']);
});
