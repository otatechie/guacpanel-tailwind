# Removing features

This is a starter kit. You are meant to cut it down.

Most features come out cleanly, but a few are threaded through shared files.
This lists what to delete and — more importantly — the shared registries you
have to edit rather than delete, because those are what break silently.

## The short way

```bash
php artisan guacpanel:remove                    # what can be removed
php artisan guacpanel:remove charts --dry-run   # what it would change
php artisan guacpanel:remove charts             # do it
```

The command deletes the feature's own files and its tests, strips its route
statements out of `routes/` (dropping any group they leave empty), removes its
entries from `navigation.js` and the settings page, deletes its permissions from
the seeder and the protected list, drops its npm packages and css imports, and
forgets the feature so it is no longer offered. It then prints what it deliberately did not touch -- route groups,
seeders, and pages you are keeping that merely reference the feature -- because
a half-correct automatic edit to a file you are keeping is worse than a clear
instruction.

Route statements are removed in the same pass as the controller, deliberately:
`Route::controller()` autoloads the class during route registration, so a
deleted controller with its route left behind takes the whole application down
-- every request, and `php artisan test` with it -- rather than just the feature.

Whether the build still passes afterwards depends on the feature. Charts, for
instance, is rendered inline by the dashboard, so that page needs editing before
it compiles again. Those leftovers are loud rather than silent, which is the
point.

It refuses to run with uncommitted changes, because its undo is git: with a
clean tree, `git checkout .` puts everything back. Because of that same check
everything left uncommitted afterwards is the command's own work, so it offers
to commit for you -- which is also how you remove several features in a row
without stopping to commit between each one. When it finishes it prints
the exact command to restore what it deleted, so changing your mind later is a
copy and paste rather than an excavation. There is no `guacpanel:restore` on
purpose -- git already does this correctly, and a second copy of your files
would only rot.

The rest of this file is the same information in prose, and covers features the
command does not know about.

## Before you start

```bash
php artisan route:list        # what exists now
npm run build && ./vendor/bin/pest
```

`tests/Feature/RouteReferenceTest.php` cross-checks every `route('...')` in
`resources/js` against Laravel's route table. If you delete a route and leave a
reference behind, that test fails with the file name. Run it after every
removal — it is the difference between finding the problem now and a user
finding it in production, because Ziggy only throws when someone reaches the
code.

## The four shared registries

Almost every feature touches some of these. They are edited, never deleted:

| File | What it holds |
|---|---|
| `routes/web.php` / `routes/auth.php` | route definitions |
| `resources/js/navigation.js` | sidebar + command palette entries |
| `app/Traits/HasProtectedPermission.php` | permissions the UI refuses to delete |
| `database/seeders/PermissionRoleSeeder.php` | the permission list itself |

`navigation.js` is read by both the sidebar and the palette, so one edit covers
both. Before this existed they were separate lists and had already drifted.

## Turn off rather than remove

These have config flags, honoured by routes, nav, palette and the scheduler.
Set them in `.env` — no code changes:

| Feature | Flag |
|---|---|
| Notifications | `APP_NOTIFICATIONS_ENABLED=false` |
| Registration | `APP_REGISTRATION_ENABLED=false` |
| Password reset | `APP_PW_RESET_ENABLED=false` |
| Two-factor | `APP_MFA_ENABLED=false` |
| Email verification | `APP_EMAIL_VERIFICATION_ENABLED=false` |
| Account deactivate / delete / restore | `APP_USER_*_ENABLED=false` |
| Demo mode | `APP_DEMO_ENABLED=false` |

A flag is a permanent second code path. If you know you will never want the
feature, delete it instead.

## Failed jobs

Delete `app/Http/Controllers/Admin/AdminFailedJobController.php`,
`app/Models/FailedJob.php`, `resources/js/Pages/Admin/IndexFailedJobPage.vue` and
`tests/Feature/AdminFailedJobControllerTest.php`.

Edit: the `admin/failed-jobs` routes, the `navigation.js` entry, and the
`view-failed-jobs` / `manage-failed-jobs` entries in `PermissionRoleSeeder.php`.
Leave the `failed_jobs` table alone -- Laravel's queue writes it regardless.

## Charts

Delete:

- `resources/js/Components/Charts/`
- `resources/js/Pages/Charts.vue`
- `app/Http/Controllers/Pages/ChartsController.php`
- `resources/css/partials/charts.css` (and its `@import` in `resources/css/app.css`)
- `resources/js/Components/__tests__/Charts.smoke.test.js`

Edit:

- `routes/web.php` — drop the `chart.index` route
- `resources/js/navigation.js` — drop the Charts entry
- `resources/js/Pages/Dashboard.vue` — it renders charts inline
- `app/Http/Controllers/Pages/DashboardController.php` — drop `financialMetrics`
- `package.json` — drop `@unovis/vue` and `@unovis/ts`

If you are dropping the sample data model too: `app/Models/FinancialMetric.php`,
`app/Services/FinancialMetricsService.php`, their migration and factory.

## Typesense search and the command palette

The palette already degrades on its own: with no API key it shows pages and
actions and says data search is unavailable. To remove the backend entirely:

- Set `SCOUT_DRIVER=null` — that alone stops all indexing.
- Delete `app/Http/Controllers/TypesenseController.php`,
  `resources/js/Components/Typesense/`, and the `/typesense/*` routes.
- Remove `use Searchable` from `app/Models/User.php` and
  `app/Models/FinancialMetric.php`.
- In `resources/js/Components/CommandPalette/CommandPalette.vue`, remove the
  `FederatedSearch` mount and the `typesenseResults` group.

To remove the palette as well, delete `resources/js/Components/CommandPalette/`
and `resources/js/composables/useCommandPalette.js`, then edit
`resources/js/Layouts/Default.vue` — it mounts `CommandPalette` and uses
`CommandPaletteTrigger` as the header search button, so both come out together.

## Backups

Delete `app/Http/Controllers/Admin/AdminBackupController.php`,
`resources/js/Pages/Admin/IndexBackupPage.vue`, and `config/backup.php`.
Remove `spatie/laravel-backup` from `composer.json`.

Edit: the `admin/backups` routes, the `navigation.js` entry, the
`view-backups` / `manage-backups` entries in `PermissionRoleSeeder.php` and
`HasProtectedPermission.php`, and the card linking to it in
`resources/js/Pages/Admin/IndexSettingPage.vue`.

## Impersonation

Delete `app/Http/Controllers/Admin/ImpersonationController.php` and
`resources/js/Components/Admin/ImpersonationBanner.vue`.

Edit:

- `routes/web.php` — the `admin/impersonate` group
- `resources/js/Layouts/Default.vue` — mounts the banner
- `app/Http/Middleware/HandleInertiaRequests.php` — shares impersonation state
- `resources/js/Pages/Admin/User/IndexUserPage.vue` — the row action
- `PermissionRoleSeeder.php` — the `impersonate-users` permission

## After any removal

```bash
npm run build
npm run lint
./vendor/bin/pest      # RouteReferenceTest catches orphaned route() calls
npx vitest run
```

Then grep for the feature's name across `resources/js` and `app/` once more.
The tests catch orphaned routes; they do not catch an orphaned import or a
settings card still linking to a page you deleted.
