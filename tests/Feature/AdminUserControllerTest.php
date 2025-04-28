<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view users']);
    Permission::firstOrCreate(['name' => 'edit users']);
    Permission::firstOrCreate(['name' => 'delete users']);
    Permission::firstOrCreate(['name' => 'manage users']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo([
        'view users',
        'edit users',
        'delete users',
        'manage users'
    ]);

    $this->viewOnlyUser = User::factory()->create();
    $this->viewOnlyUser->givePermissionTo('view users');

    $this->regularUser = User::factory()->create();
    
    $this->testToken = 'test-token';
});

test('it allows users with view permission to access user index page', function () {
    $response = $this->actingAs($this->viewOnlyUser)
        ->get(route('admin.user.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('Admin/User/IndexUserPage')
            ->has('users')
    );
});

test('it denies access to users without view permission', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('admin.user.index'));

    $response->assertForbidden();
});

test('it allows admin to view edit user page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.user.edit', $user));
        
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('Admin/User/EditUserPage')
            ->has('user')
            ->has('permissions')
            ->has('roles')
    );
});

test('it denies edit access to users with view-only permission', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($this->viewOnlyUser)
        ->get(route('admin.user.edit', $user));
        
    $response->assertForbidden();
});

test('it allows admin to update user', function () {
    $user = User::factory()->create();
    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'disable_account' => false,
        'force_password_change' => false,
        '_token' => $this->testToken
    ];

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), $updatedData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com'
    ]);
});

test('it prevents user update with invalid data', function () {
    $user = User::factory()->create();
    $invalidData = [
        'name' => '',
        'email' => 'not-an-email',
        '_token' => $this->testToken
    ];

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->from(route('admin.user.edit', $user))
        ->put(route('admin.user.update', $user), $invalidData);

    $response->assertSessionHasErrors(['name', 'email']);
});

test('it prevents user email update to existing email', function () {
    $existingUser = User::factory()->create();
    $userToUpdate = User::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->from(route('admin.user.edit', $userToUpdate))
        ->put(route('admin.user.update', $userToUpdate), [
            'name' => 'New Name',
            'email' => $existingUser->email,
            '_token' => $this->testToken
        ]);

    $response->assertSessionHasErrors(['email']);
});

test('it allows admin to delete user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.user.destroy', $user), [
            '_token' => $this->testToken
        ]);

    $response->assertRedirect(route('admin.user.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id
    ]);
});

test('it denies user update to users with view permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), [
            'name' => 'Updated Name',
            '_token' => $this->testToken
        ]);

    $response->assertForbidden();
});

test('it denies user deletion to users with view permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.user.destroy', $user), [
            '_token' => $this->testToken
        ]);

    $response->assertForbidden();
});
