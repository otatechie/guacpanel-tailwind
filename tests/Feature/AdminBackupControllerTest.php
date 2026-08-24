<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-backups']);
    Permission::firstOrCreate(['name' => 'manage-backups']);

    $this->viewUser = User::factory()->create();
    $this->viewUser->givePermissionTo('view-backups');

    $this->manageUser = User::factory()->create();
    $this->manageUser->givePermissionTo('manage-backups');

    $this->regularUser = User::factory()->create();

    Storage::fake(config('backup.backup.destination.disks')[0] ?? 'local');

    $this->testToken = 'test-token';
});

test('view permission grants access to the backup index', function () {
    $response = $this->actingAs($this->viewUser)->get(route('admin.backup.index'));

    $response->assertStatus(200);
});

test('manage permission also grants access to the backup index', function () {
    $response = $this->actingAs($this->manageUser)->get(route('admin.backup.index'));

    $response->assertStatus(200);
});

test('users without any backup permission are denied', function () {
    $response = $this->actingAs($this->regularUser)->get(route('admin.backup.index'));

    $response->assertForbidden();
});

test('view permission cannot create backups', function () {
    $response = $this->actingAs($this->viewUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.backup.create'), ['_token' => $this->testToken]);

    $response->assertForbidden();
});

test('view permission cannot delete backups', function () {
    $response = $this->actingAs($this->viewUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.backup.destroy', ['path' => base64_encode('GuacPanel/backup.zip')]), [
            '_token' => $this->testToken,
        ]);

    $response->assertForbidden();
});

test('view permission cannot download backups', function () {
    $response = $this->actingAs($this->viewUser)->get(
        route('admin.backup.download', ['path' => base64_encode('GuacPanel/backup.zip')]),
    );

    $response->assertForbidden();
});

test('manage permission can reach the delete endpoint', function () {
    $response = $this->actingAs($this->manageUser)
        ->withSession(['_token' => $this->testToken])
        ->delete(route('admin.backup.destroy', ['path' => base64_encode('GuacPanel/nonexistent.zip')]), [
            '_token' => $this->testToken,
        ]);

    // Not forbidden: redirected back with an error flash for the missing file
    $response->assertRedirect();
    $response->assertSessionHas('error');
});
