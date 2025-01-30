<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mobile_devices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('featured')->default(false);

            $table->foreignUlid('phone_brand_id')->nullable()->constrained('phone_brands')->onDelete('cascade');
            $table->foreignUlid('phone_model_id')->nullable()->constrained('phone_models')->onDelete('cascade');
            $table->foreignUlid('phone_variant_id')->nullable()->constrained('phone_variants')->onDelete('cascade');

            $table->string('listing_title');
            $table->text('listing_description');
            $table->text('damage_wear_description')->nullable();
            $table->string('carrier')->nullable();
            $table->string('color')->nullable();
            $table->string('storage_capacity')->nullable();
            $table->string('ram')->nullable();
            $table->string('region')->nullable();
            $table->string('listing_code')->nullable();
            $table->unsignedTinyInteger('battery_health')->nullable();
            $table->string('imei_number', 15)->nullable()->unique();
            $table->boolean('imei_verified')->default(false);
            $table->enum('condition', ['new', 'mint', 'good', 'fair'])->default('new');
            $table->datetime('expiry_date')->nullable();
            $table->decimal('price', 10, 2);
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index('price');
            $table->index('condition');
            $table->index(['is_active', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_devices');
    }
};
