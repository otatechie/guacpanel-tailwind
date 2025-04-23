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

test('user with view permission can access user index page', function () {
    $response = $this->actingAs($this->viewOnlyUser)
        ->get(route('admin.user.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('Admin/User/IndexUserPage')
            ->has('users')
    );
});

test('user without view permission cannot access user index page', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('admin.user.index'));

    $response->assertForbidden();
});

test('admin can view edit user page', function () {
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

test('user with view-only permission cannot access edit page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($this->viewOnlyUser)
        ->get(route('admin.user.edit', $user));
        
    $response->assertForbidden();
});

test('admin can update user', function () {
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

test('admin cannot update user with invalid data', function () {
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

test('admin cannot update user email to existing email', function () {
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

test('admin can delete user', function () {
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

test('user with view permission cannot update users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), [
            'name' => 'Updated Name',
            '_token' => $this->testToken
        ]);

    $response->assertForbidden();
});

test('user with view permission cannot delete users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.user.destroy', $user), [
            '_token' => $this->testToken
        ]);

    $response->assertForbidden();
});
