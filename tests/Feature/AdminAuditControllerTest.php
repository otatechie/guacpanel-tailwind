<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-audits']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('view-audits');

    $this->regularUser = User::factory()->create();
});

test('it redirects unauthenticated users to login page', function () {
    $this->get(route('admin.audit.index'))
        ->assertRedirect(route('login'));
});

test('it denies access to users without audit permission', function () {
    $this->actingAs($this->regularUser)
        ->get(route('admin.audit.index'))
        ->assertForbidden();
});

test('it allows access to users with audit permission', function () {
    $this->actingAs($this->adminUser)
        ->get(route('admin.audit.index'))
        ->assertStatus(200)
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Admin/IndexAuditPage')
        );
});
