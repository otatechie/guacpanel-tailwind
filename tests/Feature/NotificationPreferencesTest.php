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
    expect($this->user->notificationPreferences())->toBe(['muted_scopes' => []]);
});

test('a user can mute announcement scopes', function () {
    $this->actingAs($this->user)
        ->post(route('user.notification.preferences'), ['muted_scopes' => ['release']])
        ->assertRedirect();

    expect($this->user->fresh()->notificationPreferences())->toBe(['muted_scopes' => ['release']]);
});

test('severity is no longer something a user can mute', function () {
    // It cut across every topic at once and needed errors carved out to be safe.
    // Dismissing handles the one-off case; a scope mute handles the recurring one.
    $this->actingAs($this->user)
        ->post(route('user.notification.preferences'), [
            'muted_scopes' => ['release'],
            'muted_types' => ['info'],
        ])
        ->assertRedirect();

    expect($this->user->fresh()->notificationPreferences())->toBe(['muted_scopes' => ['release']]);
});

test('it rejects scopes that are not mutable', function () {
    // `user` scope is addressed to you personally; silencing it would mean an
    // account nobody can be told about.
    $this->actingAs($this->user)
        ->post(route('user.notification.preferences'), ['muted_scopes' => ['user']])
        ->assertSessionHasErrors('muted_scopes.0');
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

test('the system banner honours the same mutes the feed does', function () {
    AppNotification::create([
        'scope' => 'system',
        'type' => 'info',
        'title' => 'Chatty',
        'message' => 'FYI',
    ]);

    // The banner is a fixed bar on every page, so muting the system scope in
    // preferences and still being handed one was a contradiction.
    expect(bannerFor($this->user))->toHaveCount(1);

    $this->actingAs($this->user)->post(route('user.notification.preferences'), [
        'muted_scopes' => ['system'],
    ]);

    expect(bannerFor($this->user->fresh()))->toHaveCount(0);
});

/**
 * What the banner would be handed for this user.
 *
 * Driven through the middleware rather than over HTTP: the prop is only shared
 * on Inertia requests, and faking those headers in a test means matching an
 * asset-manifest version that the middleware computes per request.
 */
function bannerFor(User $user)
{
    $request = \Illuminate\Http\Request::create('/dashboard');
    $request->headers->set('X-Inertia', 'true');
    $request->setUserResolver(fn() => $user);

    (new \App\Http\Middleware\ShareSystemNotifications())->handle($request, fn() => new \Illuminate\Http\Response());

    return \Inertia\Inertia::getShared('systemNotifications');
}
