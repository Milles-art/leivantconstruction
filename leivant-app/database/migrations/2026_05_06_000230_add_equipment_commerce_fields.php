<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_for_sale')->default(true)->index()->after('region');
            $table->boolean('is_for_rent')->default(false)->index()->after('is_for_sale');
            $table->unsignedInteger('rental_price_per_day')->nullable()->after('is_for_rent');
            $table->string('availability_status')->default('available')->index()->after('rental_price_per_day');
            $table->string('equipment_condition')->default('Good working condition')->after('availability_status');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('purchase_type')->default('buy')->after('quantity');
            $table->date('rental_start_date')->nullable()->after('purchase_type');
            $table->date('rental_end_date')->nullable()->after('rental_start_date');
            $table->unsignedInteger('rental_days')->nullable()->after('rental_end_date');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('purchase_type')->default('buy')->after('quantity');
            $table->date('rental_start_date')->nullable()->after('purchase_type');
            $table->date('rental_end_date')->nullable()->after('rental_start_date');
            $table->unsignedInteger('rental_days')->nullable()->after('rental_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['purchase_type', 'rental_start_date', 'rental_end_date', 'rental_days']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['purchase_type', 'rental_start_date', 'rental_end_date', 'rental_days']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_for_sale',
                'is_for_rent',
                'rental_price_per_day',
                'availability_status',
                'equipment_condition',
            ]);
        });
    }
};
