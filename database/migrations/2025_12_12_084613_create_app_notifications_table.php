<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('scope')->default('user'); // user|system|release
            $table->string('type')->default('info'); // info|success|warning|error
            $table->string('title')->nullable();
            $table->text('message');
            $table->json('data')->nullable();

            $table->boolean('sent_as_scheduled')->default(false)->index();
            $table->timestamp('scheduled_on')->nullable()->index();
            $table->timestamp('auto_expire_on')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sent_as_scheduled', 'scheduled_on'], 'app_notifications_sched_send_idx');
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'dismissed_at']);
            $table->index(['scope', 'created_at']);
            $table->index('auto_expire_on');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
