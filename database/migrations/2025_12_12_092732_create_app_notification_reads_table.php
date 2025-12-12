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
            $table->timestamps();

            $table->unique(['app_notification_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notification_reads');
    }
};
