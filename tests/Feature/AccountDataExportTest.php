<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'Ada', 'location' => 'Cardiff']);
});

test('it requires a signed-in user', function () {
    $this->get(route('user.export'))->assertRedirect(route('login'));
});

function exportPayload($test): array
{
    $response = $test->actingAs($test->user)->get(route('user.export'))->assertOk();

    return json_decode($response->streamedContent(), true);
}

test('it exports the account, roles and preferences', function () {
    $this->user->assignRole(Role::create(['name' => 'editor']));
    $this->user->update(['notification_preferences' => ['muted_scopes' => ['release'], 'muted_types' => []]]);

    $payload = exportPayload($this);

    expect($payload['account']['name'])
        ->toBe('Ada')
        ->and($payload['account']['location'])
        ->toBe('Cardiff')
        ->and($payload['roles'])
        ->toBe(['editor'])
        ->and($payload['notification_preferences']['muted_scopes'])
        ->toBe(['release'])
        ->and($payload)
        ->toHaveKey('exported_at');
});

test('it never exports credentials', function () {
    // The model is $fillable-based with a $hidden list, but this export is
    // assembled by hand precisely so a future column cannot leak in.
    $this->user
        ->forceFill([
            'two_factor_secret' => encrypt('SECRETSEED'),
            'remember_token' => 'REMEMBERVALUE',
            'restore_token' => 'RESTOREVALUE',
        ])
        ->save();

    $response = $this->actingAs($this->user->fresh())
        ->get(route('user.export'))
        ->assertOk();
    $raw = $response->streamedContent();

    // Assert on the values, not the field names: `password_changed_at` and
    // `two_factor_enabled` are legitimately exported and would trip a
    // substring check on "password" or "two_factor".
    expect($raw)
        ->not->toContain($this->user->password)
        ->and($raw)
        ->not->toContain('REMEMBERVALUE')
        ->and($raw)
        ->not->toContain('RESTOREVALUE')
        ->and($raw)
        ->not->toContain($this->user->fresh()->two_factor_secret)
        ->and($raw)
        ->not->toContain('two_factor_recovery_codes');

    // The fact of 2FA is useful; the seed is not.
    expect(json_decode($raw, true)['account']['two_factor_enabled'])->toBeTrue();
});

test('it includes login history and notifications addressed to the user', function () {
    $this->user->loginHistory()->create(['login_at' => now(), 'ip_address' => '203.0.113.5']);

    AppNotification::create([
        'user_id' => $this->user->id,
        'scope' => 'user',
        'type' => 'info',
        'title' => 'Yours',
        'message' => 'Hello',
    ]);
    AppNotification::create([
        'scope' => 'system',
        'type' => 'info',
        'title' => 'Everyones',
        'message' => 'Broadcast',
    ]);

    $payload = exportPayload($this);

    expect($payload['login_history'][0]['ip_address'])
        ->toBe('203.0.113.5')
        // System announcements belong to everyone, not to this account.
        ->and(collect($payload['notifications'])->pluck('title')->all())
        ->toBe(['Yours']);
});

test('it downloads as a dated json file', function () {
    $response = $this->actingAs($this->user)->get(route('user.export'))->assertOk();

    expect($response->headers->get('content-type'))
        ->toContain('application/json')
        ->and($response->headers->get('content-disposition'))
        ->toContain('account-data-' . $this->user->id . '-' . now()->format('Y-m-d') . '.json');
});
