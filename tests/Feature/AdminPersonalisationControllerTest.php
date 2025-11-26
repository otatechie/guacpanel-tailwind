<?php

use App\Models\Personalisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage-personalization']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('manage-personalization');

    $this->regularUser = User::factory()->create();

    $this->personalisation = Personalisation::factory()->create();

    $this->testToken = 'test-token';
    $this->validData = [
        '_token'         => $this->testToken,
        'app_name'       => 'New App Name',
        'timezone'       => 'UTC',
        'copyright_text' => '© 2024',
    ];
});

test('it allows users with manage permission to access personalisation page', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.personalization.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Personalisation/IndexPage')
            ->has('personalisation')
            ->has('timezones')
    );
});

test('it denies access to users without manage permission', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('admin.personalization.index'));

    $response->assertForbidden();
});

test('it allows users with upload permission to upload app logo', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('logo.jpg');

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.upload'), [
            '_token'   => $this->testToken,
            'app_logo' => $file,
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['path']);

    $path = $response->json('path');
    $path = str_replace('/storage/', '', $path);
    $this->assertTrue(Storage::disk('public')->exists($path));
});

test('it allows users with upload permission to upload favicon', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('favicon.png', 32, 32);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.upload'), [
            '_token'  => $this->testToken,
            'favicon' => $file,
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['path']);

    $path = $response->json('path');
    $path = str_replace('/storage/', '', $path);
    $this->assertTrue(Storage::disk('public')->exists($path));
});

test('it denies file upload to users without upload permission', function () {
    $file = UploadedFile::fake()->image('logo.jpg');

    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.upload'), [
            '_token'   => $this->testToken,
            'app_logo' => $file,
        ]);

    $response->assertForbidden();
});

test('it allows users with delete permission to delete app logo', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('logo.jpg');
    $path = 'personalisation/'.time().'_logo.jpg';
    Storage::disk('public')->put($path, $file->getContent());

    $this->personalisation->update(['app_logo' => $path]);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.delete'), [
            '_token' => $this->testToken,
            'field'  => 'app_logo',
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->personalisation->refresh();
    $this->assertNull($this->personalisation->app_logo);
    $this->assertFalse(Storage::disk('public')->exists($path));
});

test('it allows users with delete permission to delete favicon', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('favicon.png', 32, 32);
    $path = 'personalisation/'.time().'_favicon.png';
    Storage::disk('public')->put($path, $file->getContent());

    $this->personalisation->update(['favicon' => $path]);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.delete'), [
            '_token' => $this->testToken,
            'field'  => 'favicon',
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->personalisation->refresh();
    $this->assertNull($this->personalisation->favicon);
    $this->assertFalse(Storage::disk('public')->exists($path));
});

test('it denies file deletion to users without delete permission', function () {
    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.delete'), [
            '_token' => $this->testToken,
            'field'  => 'app_logo',
        ]);

    $response->assertForbidden();
});

test('it allows users with update permission to update personalisation settings', function () {
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

test('it denies settings update to users without update permission', function () {
    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.personalization.update'), $this->validData);

    $response->assertForbidden();
});
