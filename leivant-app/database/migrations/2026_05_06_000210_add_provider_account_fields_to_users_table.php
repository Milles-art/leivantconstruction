<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('provider_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('account_type')->default('provider')->after('provider_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('provider_id');
            $table->dropColumn('account_type');
        });
    }
};
