<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('company')->nullable()->after('name');
            $table->string('region')->nullable()->after('email')->index();
            $table->string('site_location')->nullable()->after('region');
            $table->string('project_type')->nullable()->after('subject')->index();
            $table->string('project_stage')->nullable()->after('project_type');
            $table->string('budget_range')->nullable()->after('project_stage');
            $table->string('timeline')->nullable()->after('budget_range');
            $table->string('preferred_contact')->nullable()->after('timeline');
        });

        if (Schema::hasTable('services') && ! DB::table('services')->where('slug', 'site-support')->exists()) {
            DB::table('services')
                ->where('slug', 'unskilled-labour')
                ->update([
                    'name' => 'Site Support',
                    'slug' => 'site-support',
                    'summary' => 'Reliable site support for preparation, loading, trenching, cleaning, mixing, and material movement under supervision.',
                    'description' => 'Reliable site support for preparation, loading, trenching, cleaning, mixing, and material movement under supervision. Leivant reviews the project location, scope, budget range, timeline, and readiness before advising the next delivery step.',
                    'icon' => 'site-support',
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'company',
                'region',
                'site_location',
                'project_type',
                'project_stage',
                'budget_range',
                'timeline',
                'preferred_contact',
            ]);
        });

        if (Schema::hasTable('services') && ! DB::table('services')->where('slug', 'unskilled-labour')->exists()) {
            DB::table('services')
                ->where('slug', 'site-support')
                ->update([
                    'name' => 'Unskilled Labour',
                    'slug' => 'unskilled-labour',
                    'icon' => 'unskilled-labour',
                ]);
        }
    }
};
