<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('restore_token')
                ->nullable()
                ->after('profile_image_type');

            $table->boolean('auto_destroy')
                ->default(1)
                ->after('restore_token');

            $table->datetime('auto_destroy_date')
                ->nullable()
                ->after('deleted_at');

            $table->datetime('restore_date')
                ->nullable()
                ->after('auto_destroy_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('restore_token');
            $table->dropColumn('auto_destroy');
            $table->dropColumn('auto_destroy_date');
            $table->dropColumn('restore_date');
        });
    }
};
