<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('scope')->default('user'); // user|system
            $table->string('type')->default('info'); // info|success|warning|error
            $table->string('title')->nullable();
            $table->text('message');
            $table->json('data')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'dismissed_at']);
            $table->index(['scope', 'created_at']);
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
