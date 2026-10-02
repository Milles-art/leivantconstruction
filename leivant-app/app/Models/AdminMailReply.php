<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMailReply extends Model
{
    protected $fillable = [
        'admin_mail_message_id', 'user_id', 'to_email', 'subject', 'body_text',
        'sent_at', 'delivery_status', 'error_message',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(AdminMailMessage::class, 'admin_mail_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}