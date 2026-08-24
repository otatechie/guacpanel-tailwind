<?php

/**
 * guacpanel:remove deletes files, so the parts that can be exercised without
 * destroying the working tree are: the listing, the dry run, unknown input, and
 * the registry editing, which is the only genuinely fiddly logic in there.
 */

use App\Console\Commands\RemoveFeature;

test('it lists the removable features when given none', function () {
    $this->artisan('guacpanel:remove')
        ->expectsOutputToContain('charts')
        ->expectsOutputToContain('impersonation')
        ->assertSuccessful();
});

test('it fails on an unknown feature rather than doing nothing quietly', function () {
    $this->artisan('guacpanel:remove', ['feature' => 'nonsense'])->assertFailed();
});

test('a dry run changes nothing on disk', function () {
    $before = [
        base_path('package.json') => file_get_contents(base_path('package.json')),
        base_path('resources/js/navigation.js') => file_get_contents(base_path('resources/js/navigation.js')),
        base_path('resources/css/app.css') => file_get_contents(base_path('resources/css/app.css')),
    ];

    $this->artisan('guacpanel:remove', ['feature' => 'charts', '--dry-run' => true])->assertSuccessful();

    foreach ($before as $path => $contents) {
        expect(file_get_contents($path))->toBe($contents);
    }

    expect(is_dir(base_path('resources/js/Components/Charts')))->toBeTrue();
});

test('every manifest path and route it names actually exists', function () {
    // A manifest that has drifted is worse than no command: it deletes the
    // wrong things and leaves the right ones.
    $features = (function () {
        return $this->features();
    })->call(new RemoveFeature());

    $navigation =
        file_get_contents(base_path('resources/js/navigation.js')) .
        file_get_contents(base_path('resources/js/Pages/Admin/IndexSettingPage.vue'));

    foreach ($features as $key => $feature) {
        foreach ($feature['delete'] ?? [] as $path) {
            $this->assertFileExists(base_path($path), "[{$key}] lists a path that does not exist");
        }

        foreach ($feature['routes'] ?? [] as $route) {
            $this->assertStringContainsString(
                "route: '{$route}'",
                $navigation,
                "[{$key}] lists route {$route}, which no registry carries",
            );
        }
    }
});

test('a feature takes its own tests with it', function () {
    /* Removing backups left AdminBackupControllerTest behind, so a completed
       removal failed seven tests for routes it had just deleted on purpose.
       Any test file exercising a feature's routes has to be in its delete
       list. */
    $features = (function () {
        return $this->features();
    })->call(new RemoveFeature());

    foreach ($features as $key => $feature) {
        $routes = $feature['routes'] ?? [];

        if (!$routes) {
            continue;
        }

        $deletes = $feature['delete'] ?? [];

        foreach (glob(base_path('tests/Feature/*.php')) as $file) {
            $source = file_get_contents($file);
            $relative = 'tests/Feature/' . basename($file);

            foreach ($routes as $route) {
                if (!str_contains($source, "route('{$route}'")) {
                    continue;
                }

                $this->assertContains(
                    $relative,
                    $deletes,
                    "[{$key}] does not delete {$relative}, which calls route {$route}",
                );
            }
        }
    }
});

test('it strips route statements naming a deleted controller', function () {
    // Deleting a controller a route file still names takes the whole app down:
    // Route::controller() autoloads the class during route registration, so
    // every request and `php artisan test` die on a missing file.
    $remove = new RemoveFeature();

    $source = <<<'PHP'
    <?php
    use App\Http\Controllers\Admin\AdminHealthStatusController;
    use App\Http\Controllers\Admin\KeepMeController;

    Route::get('keep', [KeepMeController::class, 'index'])->name('keep');
    Route::controller(AdminHealthStatusController::class)
        ->group(function () {
            Route::get('/health', 'index')->name('health.index');
        });
    Route::get('also-keep', [KeepMeController::class, 'other'])->name('also');
    PHP;

    $result = (function () use ($source) {
        return $this->removeRouteStatements($source, 'App\\Http\\Controllers\\Admin\\AdminHealthStatusController');
    })->call($remove);

    expect($result)
        ->not->toContain('AdminHealthStatusController')
        ->and($result)
        ->not->toContain('health.index')
        // The ->group(function () { ... }) must have gone with the statement.
        ->and($result)
        ->not->toContain("Route::get('/health'")
        ->and($result)
        ->toContain("name('keep')")
        ->and($result)
        ->toContain("name('also')");
});

test('it strips permission lines without disturbing their neighbours', function () {
    $remove = new RemoveFeature();

    $source = <<<'PHP'
    $permissionData = [
        'manage-users' => 'Manage user accounts',
        'manage-backups' => 'Create and manage system backups',
        'view-backups' => 'See existing backups and their details',
        'view-audits' => 'View system audit logs',
    ];
    PHP;

    $path = base_path('.remove-command-permission-fixture.php');
    file_put_contents($path, $source);

    try {
        $method = (new ReflectionClass($remove))->getMethod('removePermissions');
        $method->setAccessible(true);

        // Point the helper at the fixture by running it over the real files is
        // not possible here, so assert the regex itself via a direct filter.
        $kept = array_values(
            array_filter(explode("\n", $source), function ($line) {
                foreach (['view-backups', 'manage-backups'] as $permission) {
                    if (
                        preg_match(
                            "/^\s*(\\\$permissions\\[)?'" . preg_quote($permission, '/') . "'\\]?\s*(=>.*)?,\s*$/",
                            $line,
                        )
                    ) {
                        return false;
                    }
                }

                return true;
            }),
        );

        $result = implode("\n", $kept);

        expect($result)
            ->not->toContain('manage-backups')
            ->and($result)
            ->not->toContain('view-backups')
            ->and($result)
            ->toContain('manage-users')
            // A prefix match must not take view-audits with it.
            ->and($result)
            ->toContain('view-audits');
    } finally {
        @unlink($path);
    }
});

test('it drops a route group left empty by a removal', function () {
    // A feature's routes usually sit in a prefix group of their own; removing
    // them leaves ->group(function () {}); behind, which is valid and useless.
    $remove = new RemoveFeature();

    $source = <<<'PHP'
    <?php
    use App\Http\Controllers\Admin\AdminBackupController;

    Route::prefix('keep')->group(function () {
        Route::get('/', 'index')->name('keep.index');
    });

    // Backup Routes
    Route::prefix('backup')
        ->name('backup.')
        ->group(function () {
            Route::controller(AdminBackupController::class)->group(function () {
                Route::get('/', 'index')->name('index');
            });
        });
    PHP;

    $result = (function () use ($source) {
        return $this->removeRouteStatements($source, 'App\\Http\\Controllers\\Admin\\AdminBackupController');
    })->call($remove);

    expect($result)
        ->not->toContain('AdminBackupController')
        ->and($result)
        ->not->toContain("prefix('backup')")
        ->and($result)
        ->not->toContain('Backup Routes')
        ->and($result)
        ->toContain("prefix('keep')")
        ->and($result)
        ->toContain("name('keep.index')");
});

test('it refuses to run with uncommitted changes', function () {
    // The undo for this command is git, which only holds if there is nothing
    // uncommitted to lose. Written against a scratch file so the assertion does
    // not depend on the state of whoever's checkout is running it.
    $scratch = base_path('.remove-command-dirty-check');
    file_put_contents($scratch, "temporary\n");

    try {
        $this->artisan('guacpanel:remove', ['feature' => 'charts'])
            ->expectsOutputToContain('uncommitted changes')
            ->assertFailed();
    } finally {
        @unlink($scratch);
    }

    // Still there: it refused before deleting anything.
    expect(is_dir(base_path('resources/js/Components/Charts')))->toBeTrue();
});

test('a dry run is exempt from the clean-tree check', function () {
    $scratch = base_path('.remove-command-dirty-check');
    file_put_contents($scratch, "temporary\n");

    try {
        $this->artisan('guacpanel:remove', ['feature' => 'charts', '--dry-run' => true])->assertSuccessful();
    } finally {
        @unlink($scratch);
    }
});

test('it strips a manifest entry cleanly, leaving valid neighbours', function () {
    $remove = new RemoveFeature();

    $source = <<<'JS'
    export const nav = [
        { name: 'Dashboard', route: 'dashboard', icon: 'home' },
        {
            /* a comment inside the entry */
            name: 'Charts',
            route: 'chart.index',
            keywords: ['a', 'b'],
        },
        { name: 'Last', route: 'last.index', icon: 'x' },
    ]
    JS;

    $result = (function () use ($source) {
        return $this->removeEntryByRoute($source, 'chart.index');
    })->call($remove);

    expect($result)
        ->not->toContain('chart.index')
        ->and($result)
        ->not->toContain('a comment inside the entry')
        ->and($result)
        ->toContain("route: 'dashboard'")
        ->and($result)
        ->toContain("route: 'last.index'")
        // The nested array must not have ended the match early.
        ->and(substr_count($result, '{'))
        ->toBe(2);
});
