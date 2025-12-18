<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('app_notification_reads', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('app_notification_id')
                ->constrained('app_notifications')
                ->cascadeOnDelete();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('u_del_notif_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->unique(['app_notification_id', 'user_id']);

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'dismissed_at']);
            $table->index(['user_id', 'u_del_notif_at'], 'anr_u_d_notif_at_idx');
            $table->index(['user_id', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notification_reads');
    }
};
