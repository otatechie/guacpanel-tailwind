<?php

use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-sessions']);
    Permission::firstOrCreate(['name' => 'manage-sessions']);

    $this->viewUser = User::factory()->create();
    $this->viewUser->givePermissionTo('view-sessions');

    $this->manageUser = User::factory()->create();
    $this->manageUser->givePermissionTo('manage-sessions');

    $this->regularUser = User::factory()->create();

    $this->otherUser = User::factory()->create();
    Session::create([
        'id' => 'other-user-session-id',
        'user_id' => $this->otherUser->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'TestAgent',
        'payload' => '',
        'last_activity' => now()->timestamp,
    ]);

    $this->testToken = 'test-token';
});

test('view permission grants access to the sessions index', function () {
    $response = $this->actingAs($this->viewUser)->get(route('admin.sessions.index'));

    $response->assertStatus(200);
});

test('manage permission also grants access to the sessions index', function () {
    $response = $this->actingAs($this->manageUser)->get(route('admin.sessions.index'));

    $response->assertStatus(200);
});

test('users without any session permission are denied', function () {
    $response = $this->actingAs($this->regularUser)->get(route('admin.sessions.index'));

    $response->assertForbidden();
});

test('view permission cannot terminate a session', function () {
    $response = $this->actingAs($this->viewUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.sessions.destroy', 'other-user-session-id'), [
            '_token' => $this->testToken,
        ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('sessions', ['id' => 'other-user-session-id']);
});

test('view permission cannot terminate all sessions for a user', function () {
    $response = $this->actingAs($this->viewUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.sessions.destroy-all', $this->otherUser->id), [
            '_token' => $this->testToken,
        ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('sessions', ['id' => 'other-user-session-id']);
});

test('manage permission can terminate another user session', function () {
    $response = $this->actingAs($this->manageUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.sessions.destroy', 'other-user-session-id'), [
            '_token' => $this->testToken,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('sessions', ['id' => 'other-user-session-id']);
});
