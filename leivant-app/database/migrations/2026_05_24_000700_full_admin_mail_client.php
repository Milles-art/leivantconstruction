<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_mail_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email')->unique();
            $table->string('display_name')->nullable();
            $table->string('imap_host');
            $table->unsignedInteger('imap_port')->default(993);
            $table->string('imap_encryption', 20)->default('ssl');
            $table->string('smtp_host');
            $table->unsignedInteger('smtp_port')->default(465);
            $table->string('smtp_encryption', 20)->default('ssl');
            $table->string('username');
            $table->text('encrypted_password');
            $table->json('folders_json')->nullable();
            $table->text('signature_html')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_sync_error')->nullable();
            $table->timestamps();
        });
    }
};