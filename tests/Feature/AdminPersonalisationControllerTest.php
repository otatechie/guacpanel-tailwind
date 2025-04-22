<?php

use App\Models\Personalisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view personalisation']);
    Permission::firstOrCreate(['name' => 'update personalisation']);
    Permission::firstOrCreate(['name' => 'upload personalisation files']);
    Permission::firstOrCreate(['name' => 'delete personalisation files']);
    
    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo([
        'view personalisation',
        'update personalisation',
        'upload personalisation files',
        'delete personalisation files'
    ]);
    
    $this->viewOnlyUser = User::factory()->create();
    $this->viewOnlyUser->givePermissionTo('view personalisation');
    
    $this->regularUser = User::factory()->create();
    $this->personalisation = Personalisation::factory()->create();

    $this->testToken = 'test-token';
    $this->validData = [
        '_token' => $this->testToken,
        'app_name' => 'New App Name',
        'timezone' => 'UTC',
        'copyright_text' => '© 2024'
    ];
});

test('user with view permission can view personalisation page', function () {
    $response = $this->actingAs($this->viewOnlyUser)
        ->get(route('admin.personalization.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('Admin/Personalisation/IndexPage')
            ->has('personalisation')
            ->has('timezones')
    );
});

test('user without view permission cannot view personalisation page', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('admin.personalization.index'));

    $response->assertForbidden();
});

test('user with upload permission can upload app logo', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('logo.jpg');
    
    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.upload'), [
            '_token' => $this->testToken,
            'app_logo' => $file
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['path']);
    
    $path = $response->json('path');
    $path = str_replace('/storage/', '', $path);
    $this->assertTrue(Storage::disk('public')->exists($path));
});

test('user with upload permission can upload favicon', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('favicon.png', 32, 32);
    
    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.upload'), [
            '_token' => $this->testToken,
            'favicon' => $file
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['path']);
    
    $path = $response->json('path');
    $path = str_replace('/storage/', '', $path);
    $this->assertTrue(Storage::disk('public')->exists($path));
});

test('user without upload permission cannot upload files', function () {
    $file = UploadedFile::fake()->image('logo.jpg');
    
    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.upload'), [
            '_token' => $this->testToken,
            'app_logo' => $file
        ]);

    $response->assertForbidden();
});

test('user with delete permission can delete app logo', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('logo.jpg');
    $path = 'personalisation/' . time() . '_logo.jpg';
    Storage::disk('public')->put($path, $file->getContent());
    
    $this->personalisation->update(['app_logo' => $path]);
    
    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.delete'), [
            '_token' => $this->testToken,
            'field' => 'app_logo'
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    
    $this->personalisation->refresh();
    $this->assertNull($this->personalisation->app_logo);
    $this->assertFalse(Storage::disk('public')->exists($path));
});

test('user with delete permission can delete favicon', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('favicon.png', 32, 32);
    $path = 'personalisation/' . time() . '_favicon.png';
    Storage::disk('public')->put($path, $file->getContent());
    
    $this->personalisation->update(['favicon' => $path]);
    
    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.delete'), [
            '_token' => $this->testToken,
            'field' => 'favicon'
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    
    $this->personalisation->refresh();
    $this->assertNull($this->personalisation->favicon);
    $this->assertFalse(Storage::disk('public')->exists($path));
});

test('user without delete permission cannot delete files', function () {
    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.delete'), [
            '_token' => $this->testToken,
            'field' => 'app_logo'
        ]);

    $response->assertForbidden();
});

test('user with update permission can update personalisation settings', function () {
    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.update'), $this->validData);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $this->personalisation->refresh();
    $this->assertEquals($this->validData['app_name'], $this->personalisation->app_name);
    $this->assertEquals($this->validData['timezone'], $this->personalisation->timezone);
    $this->assertEquals($this->validData['copyright_text'], $this->personalisation->copyright_text);
});

test('user without update permission cannot update personalisation', function () {
    $response = $this->actingAs($this->viewOnlyUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.update'), $this->validData);

    $response->assertForbidden();
});
