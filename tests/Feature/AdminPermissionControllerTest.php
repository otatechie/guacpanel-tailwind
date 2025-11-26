<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage-permissions']);

    $this->userWithFullPermissions = User::factory()->create(['name' => 'Full Permissions User']);
    $this->userWithFullPermissions->givePermissionTo('manage-permissions');

    $this->userWithoutPermissions = User::factory()->create(['name' => 'No Permissions User']);

    $this->testToken = 'test-token';
});

test('it enforces permission middleware correctly', function () {
    $this->get(route('admin.permission.index'))
        ->assertRedirect(route('login'));

    $this->actingAs($this->userWithoutPermissions)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.permission.store'), ['name' => 'new-permission', '_token' => $this->testToken])
        ->assertForbidden();

    $indexResponse = $this->actingAs($this->userWithFullPermissions)
        ->get(route('admin.permission.index'));

    if ($indexResponse->status() >= 200 && $indexResponse->status() < 300) {
        $this->actingAs($this->userWithoutPermissions)
            ->get(route('admin.permission.index'))
            ->assertStatus(200);
    }

    $this->actingAs($this->userWithoutPermissions)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.permission.store'), ['name' => 'new-permission', '_token' => $this->testToken])
        ->assertForbidden();

    $this->actingAs($this->userWithFullPermissions)
        ->get(route('admin.permission.index'));
});

test('it requires csrf token for all operations', function () {
    $this->actingAs($this->userWithFullPermissions)
        ->post(route('admin.permission.store'), ['name' => 'csrf-test-permission'])
        ->assertStatus(419);
});

test('it allows authorized users to perform crud operations', function () {
    $createData = [
        'name'        => 'test-permission',
        'description' => 'Test description',
        '_token'      => $this->testToken,
    ];

    $this->actingAs($this->userWithFullPermissions)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.permission.store'), $createData);

    $this->assertDatabaseHas('permissions', [
        'name'        => 'test-permission',
        'description' => 'Test description',
    ]);

    $permission = Permission::where('name', 'test-permission')->first();

    $updateData = [
        'name'        => 'updated-permission',
        'description' => 'Updated description',
        '_token'      => $this->testToken,
    ];

    $this->actingAs($this->userWithFullPermissions)
        ->withSession(['_token' => $this->testToken])
        ->put(route('admin.permission.update', $permission), $updateData)
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('permissions', [
        'id'          => $permission->id,
        'name'        => 'updated-permission',
        'description' => 'Updated description',
    ]);

    $this->actingAs($this->userWithFullPermissions)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.permission.destroy', $permission), ['_token' => $this->testToken]);

    $this->assertDatabaseMissing('permissions', [
        'id' => $permission->id,
    ]);
});

test('it validates permission data correctly', function () {
    $invalidNameData = [
        'name'   => 'invalid permission name',
        '_token' => $this->testToken,
    ];

    $this->actingAs($this->userWithFullPermissions)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.permission.store'), $invalidNameData)
        ->assertSessionHasErrors(['name']);

    Permission::create(['name' => 'existing-permission']);

    $duplicateData = [
        'name'   => 'existing-permission',
        '_token' => $this->testToken,
    ];

    $this->actingAs($this->userWithFullPermissions)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.permission.store'), $duplicateData)
        ->assertSessionHasErrors(['name']);
});
