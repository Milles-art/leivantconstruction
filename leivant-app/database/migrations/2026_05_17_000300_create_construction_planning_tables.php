<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('house_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('house_type')->index();
            $table->unsignedSmallInteger('base_area_sqm');
            $table->unsignedSmallInteger('area_per_bedroom_sqm')->default(14);
            $table->decimal('floor_multiplier', 5, 2)->default(1);
            $table->unsignedSmallInteger('base_duration_weeks')->default(20);
            $table->json('formulas')->nullable();
            $table->json('model_config')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('construction_phases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->decimal('duration_factor', 5, 2)->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construction_phase_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('unit', 40);
            $table->string('formula_key')->index();
            $table->decimal('waste_factor', 5, 2)->default(1.08);
            $table->string('source_hint')->default('marketplace');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('material_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('region')->nullable()->index();
            $table->unsignedInteger('price');
            $table->string('unit', 40);
            $table->string('source')->default('marketplace')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('house_type');
            $table->unsignedTinyInteger('bedrooms');
            $table->unsignedTinyInteger('floors');
            $table->string('finish_level');
            $table->string('roof_type');
            $table->string('plot_size');
            $table->unsignedInteger('budget')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('floor_area_sqm');
            $table->unsignedSmallInteger('duration_weeks');
            $table->unsignedBigInteger('total_cost');
            $table->string('budget_status')->default('not provided');
            $table->json('plan_payload');
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('project_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phase');
            $table->string('material_name');
            $table->decimal('quantity', 12, 2);
            $table->string('unit', 40);
            $table->unsignedInteger('unit_cost')->default(0);
            $table->unsignedBigInteger('estimated_cost')->default(0);
            $table->string('source')->default('marketplace');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_materials');
        Schema::dropIfExists('project_requests');
        Schema::dropIfExists('material_prices');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('construction_phases');
        Schema::dropIfExists('house_templates');
    }
};
