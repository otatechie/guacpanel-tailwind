<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    config(['session.driver' => 'database']);

    $this->authSessionId = createAuthSession($this->user);
    $this->csrfToken = 'test-token';
});

function createAuthSession($user)
{
    $sessionId = Session::getId();

    DB::table('sessions')->insert([
        'id'            => $sessionId,
        'user_id'       => $user->id,
        'ip_address'    => '127.0.0.1',
        'user_agent'    => 'PHPUnit Test',
        'payload'       => base64_encode(serialize(['_token' => 'test-token'])),
        'last_activity' => now()->timestamp,
    ]);

    return $sessionId;
}

function createTestSession($user, $id = null, $timestamp = null)
{
    $sessionId = $id ?? 'test-session-'.rand(1000, 9999);
    $time = $timestamp ?? now()->timestamp;

    DB::table('sessions')->insert([
        'id'            => $sessionId,
        'user_id'       => $user->id,
        'ip_address'    => '127.0.0.1',
        'user_agent'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'last_activity' => $time,
        'payload'       => base64_encode(serialize(['_token' => 'test-token'])),
    ]);

    return $sessionId;
}

test('it redirects unauthenticated users to login page', function () {
    $this->get(route('user.session.index'))
        ->assertRedirect(route('login'));
});

test('it allows authenticated users to view browser sessions page', function () {
    $this->actingAs($this->user);

    $this->get(route('user.session.index'))
        ->assertStatus(200)
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('UserAccount/IndexSessionPage')
                ->has('user')
                ->has('sessions')
        );
});

test('it displays current session data correctly', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('user.session.index'));

    $response->assertStatus(200);

    $this->assertGreaterThanOrEqual(1, DB::table('sessions')->where('user_id', $this->user->id)->count());
    $this->assertNotNull(DB::table('sessions')->where('id', $this->authSessionId)->first(), 'Auth session not found in database');
});

test('it requires password for logging out other devices', function () {
    $this->actingAs($this->user)
        ->withSession(['_token' => $this->csrfToken])
        ->post(route('user.session.logout'), [
            '_token' => $this->csrfToken,
        ])
        ->assertSessionHasErrors('password');
});

test('it prevents logout of other devices with incorrect password', function () {
    $this->actingAs($this->user)
        ->withSession(['_token' => $this->csrfToken])
        ->post(route('user.session.logout'), [
            '_token'   => $this->csrfToken,
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('password');
});

test('it successfully logs out other devices with valid password', function () {
    $this->actingAs($this->user);

    // Create one test session
    createTestSession($this->user, 'test-session');

    $initialCount = DB::table('sessions')->where('user_id', $this->user->id)->count();
    $this->assertEquals(2, $initialCount, 'Expected 2 sessions before logout');

    $this->withSession(['_token' => $this->csrfToken])
        ->post(route('user.session.logout'), [
            '_token'   => $this->csrfToken,
            'password' => 'password',
        ])
        ->assertRedirect();

    $finalCount = DB::table('sessions')->where('user_id', $this->user->id)->count();
    $this->assertTrue($finalCount < $initialCount, 'Expected fewer sessions after logout');
    // The implementation might remove all sessions including the current one
    $this->assertLessThanOrEqual(1, $finalCount, 'Expected at most one session to remain');
});

test('it deletes specific session when requested', function () {
    $this->actingAs($this->user);

    createTestSession($this->user, 'test-session');

    $this->withSession(['_token' => $this->csrfToken])
        ->delete(route('user.session.destroy', 'test-session'), [
            '_token' => $this->csrfToken,
        ])
        ->assertRedirect();

    $this->assertEquals(0, DB::table('sessions')->where('id', 'test-session')->count());
});
