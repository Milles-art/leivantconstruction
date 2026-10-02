<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->string('project_type')->default('new_construction')->after('email')->index();
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
            $table->string('current_status')->nullable()->after('plot_size');
            $table->string('work_type')->nullable()->after('current_status');
            $table->json('areas_to_modify')->nullable()->after('work_type');
            $table->json('brief_data')->nullable()->after('plan_payload');
            $table->text('admin_notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropColumn([
                'project_type',
                'bathrooms',
                'current_status',
                'work_type',
                'areas_to_modify',
                'brief_data',
                'admin_notes',
            ]);
        });
    }
};
