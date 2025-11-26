<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personalisations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('app_name')->nullable();
            $table->string('app_logo')->nullable();
            $table->string('app_logo_dark')->nullable();
            $table->string('favicon')->nullable();
            $table->string('copyright_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalisations');
    }
};
