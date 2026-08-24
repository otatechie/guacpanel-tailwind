<?php

/**
 * Every route('name') the frontend calls must exist in Laravel's route table.
 *
 * This is the guardrail for removing a feature. Deleting a route is the easy
 * part; the risk is the references left behind in the sidebar, the command
 * palette or a page, because Ziggy only throws when a user reaches that code --
 * in production, long after the change. These assertions move that failure to
 * CI, the moment the route goes.
 *
 * It also catches the reverse: a route renamed on the PHP side while the JS
 * still asks for the old name.
 */

use Illuminate\Support\Facades\Route;

/**
 * Names that exist only when their feature flag is on, so they are absent from
 * the test environment's route table but perfectly valid in an app that enables
 * the feature. Keep this list short -- every entry is a name this test can no
 * longer protect.
 */
const CONFIG_GATED_ROUTES = [
    // routes/auth.php, behind guacpanel.email_verification_enabled
    'verification.send',
];

function jsFiles(): array
{
    $files = [];
    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('js')));

    foreach ($dir as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['vue', 'js'], true)) {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

/** @return array<string, string[]> route name => files referencing it */
function routeReferencesInJs(): array
{
    $refs = [];

    foreach (jsFiles() as $path) {
        $source = file_get_contents($path);

        // route('name') and route("name") -- ignores template literals, which
        // are dynamic by definition.
        preg_match_all('/\broute\(\s*[\'"]([a-zA-Z0-9_.\-]+)[\'"]/', $source, $matches);

        foreach ($matches[1] as $name) {
            $refs[$name][] = str_replace(resource_path('js') . '/', '', $path);
        }
    }

    return $refs;
}

test('every route name used in the frontend exists', function () {
    $defined = collect(Route::getRoutes()->getRoutesByName())
        ->keys()
        ->all();
    $missing = [];

    foreach (routeReferencesInJs() as $name => $files) {
        if (in_array($name, $defined, true)) {
            continue;
        }

        if (in_array($name, CONFIG_GATED_ROUTES, true)) {
            continue;
        }

        $missing[] = sprintf('  %s  <- %s', $name, implode(', ', array_unique($files)));
    }

    expect($missing)->toBeEmpty(
        "The frontend calls route names that no longer exist.\n" .
            "Either restore the route or remove the reference:\n\n" .
            implode("\n", $missing) .
            "\n",
    );
});

test('every config-gated exemption is still referenced by the frontend', function () {
    // Stops the exemption list outliving the reference it was added for.
    $referenced = array_keys(routeReferencesInJs());

    foreach (CONFIG_GATED_ROUTES as $name) {
        $this->assertContains(
            $name,
            $referenced,
            "CONFIG_GATED_ROUTES lists {$name}, but no frontend file calls it. Drop the exemption.",
        );
    }
});

test('the frontend references at least one route', function () {
    // Guards the regex itself: if it silently stopped matching, the assertion
    // above would pass over an empty set and prove nothing.
    expect(routeReferencesInJs())->not->toBeEmpty();
});
