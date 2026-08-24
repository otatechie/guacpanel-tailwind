<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /* The admin UI groups permissions by the part after the action verb, so these
       two split into groups of their own: "manage-personalization" sat apart from
       its four "-personalisation" siblings, and "dashboard-view" is reversed, so
       it produced a group called "View" holding one item called "Dashboard".
       Renaming leaves the row id alone, so existing role and user grants survive. */
    private const RENAMES = [
        'manage-personalization' => 'manage-personalisation',
        'dashboard-view' => 'view-dashboard',
    ];

    public function up(): void
    {
        foreach (self::RENAMES as $from => $to) {
            DB::table('permissions')
                ->where('name', $from)
                ->update(['name' => $to]);
        }

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        foreach (self::RENAMES as $from => $to) {
            DB::table('permissions')
                ->where('name', $to)
                ->update(['name' => $from]);
        }

        app()['cache']->forget('spatie.permission.cache');
    }
};
