<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage-users']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('manage-users');

    $this->viewOnlyUser = User::factory()->create();
    // Create a view-only permission for testing restricted access
    Permission::firstOrCreate(['name' => 'view-users']);
    $this->viewOnlyUser->givePermissionTo('view-users');

    $this->regularUser = User::factory()->create();

    $this->testToken = 'test-token';
});

test('it allows users with view permission to access user index page', function () {
    $response = $this->actingAs($this->viewOnlyUser)->get(route('admin.user.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn($page) => $page->component('Admin/User/IndexUserPage')->has('users'));
});

test('it denies access to users without view permission', function () {
    $response = $this->actingAs($this->regularUser)->get(route('admin.user.index'));

    $response->assertForbidden();
});

test('it allows admin to view edit user page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->adminUser)->get(route('admin.user.edit', $user));

    $response->assertStatus(200);
    // The full permission catalogue is no longer sent: the page shows the role
    // plus any direct grants the account already has, and assigns neither.
    $response->assertInertia(
        fn($page) => $page
            ->component('Admin/User/EditUserPage')
            ->has('user')
            ->has('user.permissions')
            ->has('rolePermissionCount')
            ->has('roles')
            ->missing('permissions'),
    );
});

test('it denies edit access to users with view-only permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)->get(route('admin.user.edit', $user));

    $response->assertForbidden();
});

test('it allows admin to update user', function () {
    $user = User::factory()->create();
    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'disable_account' => false,
        'force_password_change' => false,
        '_token' => $this->testToken,
    ];

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), $updatedData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('it prevents user update with invalid data', function () {
    $user = User::factory()->create();
    $invalidData = [
        'name' => '',
        'email' => 'not-an-email',
        '_token' => $this->testToken,
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
            '_token' => $this->testToken,
        ]);

    $response->assertSessionHasErrors(['email']);
});

test('it allows admin to delete user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.user.destroy', $user), [
            '_token' => $this->testToken,
        ]);

    // Back to the list you deleted from, not off to the deleted-users page.
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);
});

test('it denies user update to users without manage permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), [
            'name' => 'Updated Name',
            '_token' => $this->testToken,
        ]);

    $response->assertForbidden();
});

test('it denies user deletion to users without manage permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.user.destroy', $user), [
            '_token' => $this->testToken,
        ]);

    $response->assertForbidden();
});

test('it filters the user list by status and keeps the filter in the response', function () {
    $locked = User::factory()->create(['name' => 'Locked Person', 'account_locked' => true]);
    User::factory()->create(['name' => 'Fine Person', 'account_locked' => false]);

    $response = $this->actingAs($this->adminUser)->get(route('admin.user.index', ['status' => 'locked']));

    $response->assertInertia(
        fn($page) => $page
            ->where('users.data', fn($rows) => collect($rows)->pluck('id')->all() === [$locked->id])
            // Without this the next search/sort/page request rebuilds its query
            // string without the status and the filter silently drops.
            ->where('filters.status', 'locked'),
    );
});

test('it reports whether an account is locked', function () {
    $user = User::factory()->create(['account_locked' => true]);

    $response = $this->actingAs($this->adminUser)->get(route('admin.user.index'));

    $response->assertInertia(
        fn($page) => $page->where(
            'users.data',
            fn($rows) => collect($rows)->firstWhere('id', $user->id)['account_locked'] === true,
        ),
    );
});

test('it returns to the user list after deleting from that users edit page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->from(route('admin.user.edit', $user->id))
        ->delete(route('admin.user.destroy', $user), ['_token' => $this->testToken]);

    // back() would land on the edit page for a soft-deleted user, which 404s.
    $response->assertRedirect(route('admin.user.index'));
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

test('it removes the role when the role field is submitted empty', function () {
    $role = Role::firstOrCreate(['name' => 'editor']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), [
            '_token' => $this->testToken,
            'name' => $user->name,
            'email' => $user->email,
            'role' => '',
        ]);

    // filled() used to skip an empty value, so clearing the combobox and saving
    // reported success while silently keeping the old role.
    expect($user->fresh()->roles)->toHaveCount(0);
});

test('it drops a direct permission left out of the submitted list', function () {
    $permission = Permission::firstOrCreate(['name' => 'view-backups']);
    $user = User::factory()->create();
    $user->givePermissionTo($permission);

    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), [
            '_token' => $this->testToken,
            'name' => $user->name,
            'email' => $user->email,
            'permissions' => [],
        ]);

    expect($user->fresh()->getDirectPermissions())->toHaveCount(0);
});

test('a partial update leaves account flags it did not submit alone', function () {
    $user = User::factory()->create([
        'disable_account' => true,
        'force_password_change' => true,
        'auto_destroy' => true,
    ]);

    // What the quick-edit sheet sends: identity only.
    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.user.update', $user), [
            '_token' => $this->testToken,
            'name' => 'Renamed',
            'email' => $user->email,
        ]);

    $fresh = $user->fresh();

    expect($fresh->name)
        ->toBe('Renamed')
        ->and($fresh->disable_account)
        ->toBeTrue()
        ->and($fresh->force_password_change)
        ->toBeTrue()
        ->and($fresh->auto_destroy)
        ->toBeTrue();
});
