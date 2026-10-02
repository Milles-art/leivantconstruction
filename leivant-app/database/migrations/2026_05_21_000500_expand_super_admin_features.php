<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('provider')->after('account_type')->index();
            });
        }

        if (Schema::hasTable('inquiries')) {
            Schema::table('inquiries', function (Blueprint $table) {
                if (! Schema::hasColumn('inquiries', 'priority')) {
                    $table->string('priority')->default('normal')->after('status')->index();
                }

                if (! Schema::hasColumn('inquiries', 'assigned_to')) {
                    $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('inquiries', 'follow_up_at')) {
                    $table->timestamp('follow_up_at')->nullable()->after('responded_at')->index();
                }

                if (! Schema::hasColumn('inquiries', 'internal_notes')) {
                    $table->text('internal_notes')->nullable()->after('response');
                }
            });
        }

        if (! Schema::hasTable('project_documents')) {
            Schema::create('project_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_request_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('path');
                $table->string('document_type')->default('general')->index();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_documents');

        if (Schema::hasTable('inquiries')) {
            Schema::table('inquiries', function (Blueprint $table) {
                if (Schema::hasColumn('inquiries', 'internal_notes')) {
                    $table->dropColumn('internal_notes');
                }

                if (Schema::hasColumn('inquiries', 'follow_up_at')) {
                    $table->dropColumn('follow_up_at');
                }

                if (Schema::hasColumn('inquiries', 'assigned_to')) {
                    $table->dropConstrainedForeignId('assigned_to');
                }

                if (Schema::hasColumn('inquiries', 'priority')) {
                    $table->dropColumn('priority');
                }
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
