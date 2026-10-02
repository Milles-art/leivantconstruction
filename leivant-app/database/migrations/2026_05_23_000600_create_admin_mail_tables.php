<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_mail_messages', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->string('message_id')->nullable()->index();
            $table->string('direction', 20)->default('inbound')->index();
            $table->string('mailbox')->default('INBOX');
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->text('to_email')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->timestamp('received_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->string('status', 40)->default('open')->index();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->unique(['mailbox', 'uid']);
        });

        Schema::create('admin_mail_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_mail_message_id')->constrained('admin_mail_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('to_email');
            $table->string('subject')->nullable();
            $table->longText('body_text');
            $table->timestamp('sent_at')->nullable();
            $table->string('delivery_status', 40)->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_mail_replies');
        Schema::dropIfExists('admin_mail_messages');
    }
};