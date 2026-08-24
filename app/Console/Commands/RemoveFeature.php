<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use ReflectionClass;

/**
 * Removes an optional feature from the starter kit.
 *
 * This does the mechanical part -- deleting files, stripping entries out of the
 * shared registries, dropping npm packages and css imports -- and then tells you
 * plainly what it could not do. It deliberately does not try to rewrite route
 * groups or pages that merely reference the feature: a half-correct automatic
 * edit to a file you are keeping is worse than a clear instruction.
 *
 * See REMOVING.md for the same information in prose.
 */
class RemoveFeature extends Command
{
    protected $signature = 'guacpanel:remove
                            {feature? : The feature to remove}
                            {--dry-run : Show what would change without touching anything}
                            {--force : Skip the confirmation prompt and the clean-tree check}';

    protected $description = 'Remove an optional feature and its registry entries';

    /** Verified against the tree; see REMOVING.md. */
    private function features(): array
    {
        return [
            'charts' => [
                'label' => 'Charts (Unovis)',
                'delete' => [
                    'resources/js/Components/Charts',
                    'resources/js/Pages/Charts.vue',
                    'app/Http/Controllers/Pages/ChartsController.php',
                    'resources/css/partials/charts.css',
                    'resources/js/Components/__tests__/Charts.smoke.test.js',
                ],
                'routes' => ['chart.index'],
                'npm' => ['@unovis/vue', '@unovis/ts'],
                'css' => ['@css/partials/charts.css'],
                'manual' => [
                    'resources/js/Pages/Dashboard.vue' => 'renders charts inline; strip those sections',
                    'app/Http/Controllers/Pages/DashboardController.php' => 'drop the financialMetrics payload',
                ],
            ],
            'backups' => [
                'label' => 'Backups',
                'delete' => [
                    'app/Http/Controllers/Admin/AdminBackupController.php',
                    'resources/js/Pages/Admin/IndexBackupPage.vue',
                    'config/backup.php',
                    'tests/Feature/AdminBackupControllerTest.php',
                ],
                'routes' => ['admin.backup.index'],
                'permissions' => ['view-backups', 'manage-backups'],
                'composer' => ['spatie/laravel-backup'],
            ],
            'health' => [
                'label' => 'Health checks',
                'delete' => [
                    'app/Http/Controllers/Admin/AdminHealthStatusController.php',
                    'resources/js/Pages/Monitoring/IndexPage.vue',
                ],
                'routes' => ['admin.health.index'],
                'permissions' => ['view-health'],
                'composer' => ['spatie/laravel-health'],
                'manual' => [
                    'app/Providers/AppHealthServiceProvider.php' => 'delete, and drop it from bootstrap/providers.php',
                ],
            ],
            'audits' => [
                'label' => 'Audit trail',
                'delete' => [
                    'app/Http/Controllers/Admin/AdminAuditController.php',
                    'resources/js/Pages/Admin/IndexAuditPage.vue',
                    'tests/Feature/AdminAuditControllerTest.php',
                ],
                'routes' => ['admin.audit.index'],
                'permissions' => ['view-audits'],
                'composer' => ['owen-it/laravel-auditing'],
                'manual' => [
                    'resources/js/Pages/Admin/IndexManageSettingPage.vue' =>
                        'an inline Activity log link -- RouteReferenceTest will name it if you forget',
                    'app/Models' => 'remove the Auditable contract and trait from every model that uses them',
                ],
            ],
            'impersonation' => [
                'label' => 'User impersonation',
                'delete' => [
                    'app/Http/Controllers/Admin/ImpersonationController.php',
                    'resources/js/Components/Admin/ImpersonationBanner.vue',
                    'tests/Feature/ImpersonationControllerTest.php',
                ],
                'manual' => [
                    'resources/js/Layouts/Default.vue' => 'mounts ImpersonationBanner',
                    'app/Http/Middleware/HandleInertiaRequests.php' => 'shares the impersonation session state',
                    'resources/js/Pages/Admin/User/IndexUserPage.vue' => 'remove the Impersonate row action',
                ],
                'permissions' => ['impersonate-users'],
            ],
        ];
    }

    /** Files holding `route: '<name>'` manifest entries the command can edit. */
    private const REGISTRIES = ['resources/js/navigation.js', 'resources/js/Pages/Admin/IndexSettingPage.vue'];

    public function handle(): int
    {
        $features = $this->features();
        $name = $this->argument('feature');

        if (!$name || !isset($features[$name])) {
            if ($name) {
                $this->components->error("Unknown feature [{$name}].");
            }

            $this->line('Available features:');
            foreach ($features as $key => $feature) {
                $this->line(sprintf('  <fg=cyan>%-16s</> %s', $key, $feature['label']));
            }
            $this->newLine();
            $this->line('Features with a config flag (notifications, registration, 2FA and');
            $this->line('others) are turned off in .env instead. See REMOVING.md.');

            return $name ? self::FAILURE : self::SUCCESS;
        }

        $feature = $features[$name];
        $dryRun = (bool) $this->option('dry-run');

        $this->components->info(($dryRun ? 'Planned removal: ' : 'Removing: ') . $feature['label']);
        $this->plan($feature);

        if ($dryRun) {
            $this->newLine();
            $this->components->warn('Dry run: nothing was changed.');

            return self::SUCCESS;
        }

        if (!$this->option('force')) {
            if (!$this->ensureRecoverable()) {
                return self::FAILURE;
            }

            if (!$this->confirm('Apply these changes?', false)) {
                $this->components->warn('Aborted.');

                return self::SUCCESS;
            }
        }

        $this->apply($feature);
        $this->forgetFeature($name);
        $this->finish($feature);

        return self::SUCCESS;
    }

    private function plan(array $feature): void
    {
        foreach ($feature['delete'] ?? [] as $path) {
            $exists = File::exists(base_path($path));
            $this->line(sprintf('  <fg=red>delete</>  %s%s', $path, $exists ? '' : ' <fg=gray>(already gone)</>'));
        }

        foreach ($feature['routes'] ?? [] as $route) {
            foreach (self::REGISTRIES as $registry) {
                if (str_contains((string) @file_get_contents(base_path($registry)), "'{$route}'")) {
                    $this->line("  <fg=yellow>edit</>    {$registry} <fg=gray>(drop {$route})</>");
                }
            }
        }

        foreach ($this->deletedControllers($feature) as $class) {
            $short = class_basename($class);

            foreach (self::ROUTE_FILES as $routeFile) {
                if (str_contains((string) @file_get_contents(base_path($routeFile)), $short . '::class')) {
                    $this->line("  <fg=yellow>edit</>    {$routeFile} <fg=gray>(drop routes using {$short})</>");
                }
            }
        }

        foreach ($feature['permissions'] ?? [] as $permission) {
            foreach (self::PERMISSION_FILES as $file) {
                if (str_contains((string) @file_get_contents(base_path($file)), "'{$permission}'")) {
                    $this->line("  <fg=yellow>edit</>    {$file} <fg=gray>(drop {$permission})</>");
                }
            }
        }

        foreach ($feature['npm'] ?? [] as $package) {
            $this->line("  <fg=yellow>edit</>    package.json <fg=gray>(drop {$package})</>");
        }

        foreach ($feature['css'] ?? [] as $import) {
            $this->line("  <fg=yellow>edit</>    resources/css/app.css <fg=gray>(drop {$import})</>");
        }
    }

    private function apply(array $feature): void
    {
        foreach ($feature['delete'] ?? [] as $path) {
            $full = base_path($path);

            if (File::isDirectory($full)) {
                File::deleteDirectory($full);
            } elseif (File::exists($full)) {
                File::delete($full);
            }
        }

        foreach (self::REGISTRIES as $registry) {
            $full = base_path($registry);

            if (!File::exists($full)) {
                continue;
            }

            $source = File::get($full);

            foreach ($feature['routes'] ?? [] as $route) {
                $source = $this->removeEntryByRoute($source, $route);
            }

            File::put($full, $source);
        }

        foreach ($this->deletedControllers($feature) as $class) {
            foreach (self::ROUTE_FILES as $routeFile) {
                $path = base_path($routeFile);

                if (File::exists($path)) {
                    File::put($path, $this->removeRouteStatements(File::get($path), $class));
                }
            }
        }

        if ($feature['permissions'] ?? false) {
            $this->removePermissions($feature['permissions']);
        }

        if ($feature['npm'] ?? false) {
            $this->removeNpmPackages($feature['npm']);
        }

        foreach ($feature['css'] ?? [] as $import) {
            $path = base_path('resources/css/app.css');
            $source = File::get($path);
            $source = preg_replace('/^@import\s+[\'"]' . preg_quote($import, '/') . '[\'"];\s*\R/m', '', $source);
            File::put($path, $source);
        }
    }

    /**
     * Drop the object literal containing `route: '<name>'`.
     *
     * Brace counting rather than a regex: these entries contain nested arrays
     * and comments, and a lazy match would stop at the first closing brace.
     */
    private function removeEntryByRoute(string $source, string $route): string
    {
        $needle = "route: '{$route}'";
        $at = strpos($source, $needle);

        if ($at === false) {
            return $source;
        }

        $start = strrpos(substr($source, 0, $at), '{');

        if ($start === false) {
            return $source;
        }

        $depth = 0;
        $end = null;

        for ($i = $start, $len = strlen($source); $i < $len; $i++) {
            if ($source[$i] === '{') {
                $depth++;
            } elseif ($source[$i] === '}') {
                $depth--;

                if ($depth === 0) {
                    $end = $i;
                    break;
                }
            }
        }

        if ($end === null) {
            return $source;
        }

        // Take the trailing comma and the blank line the entry leaves behind.
        while ($end + 1 < strlen($source) && in_array($source[$end + 1], [',', "\r"], true)) {
            $end++;
        }

        if ($end + 1 < strlen($source) && $source[$end + 1] === "\n") {
            $end++;
        }

        // And its own indentation, so the next entry does not inherit it.
        while ($start > 0 && in_array($source[$start - 1], [' ', "\t"], true)) {
            $start--;
        }

        return substr($source, 0, $start) . substr($source, $end + 1);
    }

    private function removeNpmPackages(array $packages): void
    {
        $path = base_path('package.json');
        $manifest = json_decode(File::get($path), true);

        foreach (['dependencies', 'devDependencies'] as $section) {
            foreach ($packages as $package) {
                unset($manifest[$section][$package]);
            }
        }

        File::put($path, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }

    /**
     * This command deletes directories, and its undo is git. That only holds if
     * there is nothing uncommitted to lose, so refuse otherwise rather than put
     * someone in a position where `git checkout .` costs them their own work.
     */
    private function ensureRecoverable(): bool
    {
        $inRepo = Process::path(base_path())->run('git rev-parse --is-inside-work-tree');

        if (!$inRepo->successful()) {
            $this->components->warn('Not a git repository, so there is no undo for this.');

            return $this->confirm('Continue anyway?', false);
        }

        $status = Process::path(base_path())->run('git status --porcelain');

        if (trim($status->output()) === '') {
            return true;
        }

        $this->components->error('You have uncommitted changes.');
        $this->line('  Commit or stash them first. This deletes files, and a clean tree is');
        $this->line('  what makes `git checkout .` a complete undo.');
        $this->newLine();
        $this->line('  <fg=gray>git stash        # or: git commit -am "wip"</>');
        $this->line('  <fg=gray>--force          # to skip this check</>');

        return false;
    }

    /** Route files whose statements may name a deleted controller. */
    private const ROUTE_FILES = ['routes/web.php', 'routes/auth.php'];

    /**
     * Deleting a controller that a route file still names takes the whole app
     * down, not just the feature: Route::controller() and the HasMiddleware
     * check both autoload the class during route registration, so every request
     * -- and `php artisan test` -- dies on a missing file. The route statements
     * have to go in the same pass as the file.
     */
    private function removeRouteStatements(string $source, string $class): string
    {
        $short = class_basename($class);

        $source = preg_replace('/^use\s+' . preg_quote($class, '/') . ';\s*\R/m', '', $source);

        while (($at = strpos($source, $short . '::class')) !== false) {
            [$start, $end] = $this->statementBounds($source, $at);

            if ($start === null || $end === null) {
                break;
            }

            $source = substr($source, 0, $start) . substr($source, $end + 1);
        }

        return preg_replace('/\R{3,}/', "\n\n", $this->dropEmptyGroups($source));
    }

    /**
     * A feature's routes often sit inside a prefix group of their own. Removing
     * them leaves `->group(function () {});` behind, which is valid, does
     * nothing, and keeps a comment for a feature that is gone.
     */
    private function dropEmptyGroups(string $source): string
    {
        while (preg_match('/->group\(function \(\) \{\s*\}\)/', $source, $m, PREG_OFFSET_CAPTURE)) {
            [$start, $end] = $this->statementBounds($source, $m[0][1]);

            if ($start === null || $end === null) {
                break;
            }

            $source = substr($source, 0, $start) . substr($source, $end + 1);
        }

        return $source;
    }

    /**
     * The whole `Route::...;` statement around $at, brace- and paren-aware so a
     * ->group(function () { ... }) is taken with it.
     *
     * @return array{0: ?int, 1: ?int}
     */
    private function statementBounds(string $source, int $at): array
    {
        $start = $at;

        while ($start > 0 && !in_array($source[$start - 1], [';', '{', '}'], true)) {
            $start--;
        }

        $depth = 0;
        $end = null;

        for ($i = $start, $len = strlen($source); $i < $len; $i++) {
            $char = $source[$i];

            if (in_array($char, ['(', '{', '['], true)) {
                $depth++;
            } elseif (in_array($char, [')', '}', ']'], true)) {
                $depth--;
            } elseif ($char === ';' && $depth === 0) {
                $end = $i;
                break;
            }
        }

        if ($end === null) {
            return [null, null];
        }

        if ($end + 1 < strlen($source) && $source[$end + 1] === "\n") {
            $end++;
        }

        /* Keep the newline that terminates the previous statement, then cut from
           the start of the next line. Walking back over it instead -- which is
           right for the JS registries -- pulls the previous statement's closer
           onto the same line as the following one. */
        if ($start < strlen($source) && $source[$start] === "\n") {
            $start++;
        }

        return [$start, $end];
    }

    /** FQCNs for any controller this feature deletes. */
    private function deletedControllers(array $feature): array
    {
        $classes = [];

        foreach ($feature['delete'] ?? [] as $path) {
            if (preg_match('#^app/(Http/Controllers/.+)\.php$#', $path, $m)) {
                $classes[] = 'App\\' . str_replace('/', '\\', $m[1]);
            }
        }

        return $classes;
    }

    /**
     * Drop the feature from this command's own manifest.
     *
     * Without this, a removal leaves the manifest naming files that no longer
     * exist -- which fails the drift test for good, and keeps offering a feature
     * that is already gone.
     */
    private function forgetFeature(string $name): void
    {
        $path = (new ReflectionClass($this))->getFileName();
        $source = File::get($path);

        $needle = "'{$name}' => [";
        $start = strpos($source, $needle);

        if ($start === false) {
            return;
        }

        $depth = 0;
        $end = null;

        for ($i = $start + strlen($needle) - 1, $len = strlen($source); $i < $len; $i++) {
            if ($source[$i] === '[') {
                $depth++;
            } elseif ($source[$i] === ']') {
                $depth--;

                if ($depth === 0) {
                    $end = $i;
                    break;
                }
            }
        }

        if ($end === null) {
            return;
        }

        if ($end + 1 < strlen($source) && $source[$end + 1] === ',') {
            $end++;
        }

        if ($end + 1 < strlen($source) && $source[$end + 1] === "\n") {
            $end++;
        }

        while ($start > 0 && in_array($source[$start - 1], [' ', "\t"], true)) {
            $start--;
        }

        File::put($path, substr($source, 0, $start) . substr($source, $end + 1));
    }

    /** Where a permission name is written out, one per line. */
    private const PERMISSION_FILES = [
        'database/seeders/PermissionRoleSeeder.php',
        'app/Traits/HasProtectedPermission.php',
    ];

    /**
     * Permissions are single lines -- `'name' => 'description',` in the seeder,
     * `'name',` in the protected list, and `$permissions['name'],` where a role
     * is granted one. Whole-line removal is enough; nothing here spans lines.
     */
    private function removePermissions(array $permissions): void
    {
        foreach (self::PERMISSION_FILES as $file) {
            $path = base_path($file);

            if (!File::exists($path)) {
                continue;
            }

            $lines = explode("\n", File::get($path));

            $kept = array_filter($lines, function ($line) use ($permissions) {
                foreach ($permissions as $permission) {
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
            });

            File::put($path, implode("\n", $kept));
        }
    }

    private function finish(array $feature): void
    {
        $this->newLine();
        $this->components->info('Done. These need you:');

        foreach ($feature['manual'] ?? [] as $file => $what) {
            $this->line(sprintf('  <fg=cyan>%s</>', $file));
            $this->line("      {$what}");
        }

        foreach ($feature['composer'] ?? [] as $package) {
            $this->line('  <fg=cyan>composer</>');
            $this->line("      composer remove {$package}");
        }

        $this->newLine();
        $this->line('Then verify. RouteReferenceTest will name any route() call left behind:');
        $this->line('  <fg=gray>./vendor/bin/pest && npm run lint:check && npm run build</>');

        $this->restoreHint($feature);
    }

    /**
     * Deleted files live in git history, so there is no need for this command to
     * keep its own copies -- but nobody should have to work out the incantation
     * under pressure. Printed at the point it becomes useful.
     */
    private function restoreHint(array $feature): void
    {
        $paths = $feature['delete'] ?? [];

        if (!$paths) {
            return;
        }

        $this->newLine();
        $this->line('<fg=gray>To put it back:</>');
        $this->line('  <fg=gray>git checkout HEAD -- ' . implode(' ', $paths) . '</>');

        if ($feature['npm'] ?? false) {
            // By name, not by restoring package.json: that file carries every
            // other dependency change you have made since.
            $this->line('  <fg=gray>npm install ' . implode(' ', $feature['npm']) . '</>');
        }

        $byHand = [];

        if ($feature['routes'] ?? false) {
            $byHand[] = 'its entry in resources/js/navigation.js';
        }

        if ($feature['css'] ?? false) {
            $byHand[] = 'its @import in resources/css/app.css';
        }

        if ($byHand) {
            $this->line('  <fg=gray># then re-add ' . implode(' and ', $byHand) . ' by hand --</>');
            $this->line('  <fg=gray># checking those out wholesale would revert your other edits to them</>');
        }

        $this->line(
            '  <fg=gray># no history? git remote add upstream ' .
                'https://github.com/otatechie/guacpanel-tailwind.git && git fetch upstream --tags</>',
        );
    }
}
