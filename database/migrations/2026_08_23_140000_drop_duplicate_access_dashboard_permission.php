<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up(): void
    {
        // Duplicate of view-dashboard: it gated no route, and the dashboard has
        // no permission middleware at all.
        Permission::where('name', 'access-dashboard')->delete();
    }

    public function down(): void
    {
        Permission::firstOrCreate(
            ['name' => 'access-dashboard'],
            ['guard_name' => 'web', 'description' => 'Access admin dashboard'],
        );
    }
};
