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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->json('filter_properties')->nullable();
            $table->timestamps();
        });

        Schema::create('product_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('measure')->nullable();
            $table->string('type')->default('string');
            $table->decimal('range_step', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->foreignId('product_group_id')->constrained('product_groups')->cascadeOnDelete();
            $table->jsonb('properties')->nullable();
            $table->bigInteger('popularity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::statement('CREATE INDEX idx_producs_properties_gin ON products USING gin (properties jsonb_path_ops)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_producs_properties_gin');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_groups');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('properties');
    }
};
