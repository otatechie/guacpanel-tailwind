<?php

/**
 * macOS resolves paths case-insensitively and Linux does not, so a casing
 * mismatch passes locally and fails only in CI. This repo has hit that three
 * times (a Vue import, and inertia.testing.page_paths). These assertions fail
 * on any platform.
 */
function resolvesWithExactCasing(string $path): bool
{
    $path = rtrim($path, DIRECTORY_SEPARATOR);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), fn($p) => $p !== '');
    $current = DIRECTORY_SEPARATOR;

    foreach ($parts as $part) {
        if (!is_dir($current)) {
            return false;
        }

        if (!in_array($part, scandir($current), true)) {
            return false;
        }

        $current .= $part . DIRECTORY_SEPARATOR;
    }

    return file_exists(rtrim($current, DIRECTORY_SEPARATOR));
}

test('configured inertia page paths exist with exact casing', function () {
    $paths = config('inertia.testing.page_paths');

    expect($paths)->not->toBeEmpty();

    foreach ($paths as $path) {
        expect(resolvesWithExactCasing($path))->toBeTrue(
            "Configured Inertia page path does not resolve case-sensitively: {$path}",
        );
    }
});

test('every rendered inertia page component exists with exact casing', function () {
    $pagePath = config('inertia.testing.page_paths')[0];

    $controllers = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(app_path(), FilesystemIterator::SKIP_DOTS),
    );

    $missing = [];

    foreach ($controllers as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        preg_match_all("/Inertia::render\(\s*'([^']+)'/", file_get_contents($file->getPathname()), $matches);

        foreach ($matches[1] as $component) {
            if (!resolvesWithExactCasing($pagePath . '/' . $component . '.vue')) {
                $missing[] = $component . '  (' . basename($file->getPathname()) . ')';
            }
        }
    }

    expect($missing)->toBe([], 'Inertia components that do not resolve case-sensitively: ' . implode(', ', $missing));
});
