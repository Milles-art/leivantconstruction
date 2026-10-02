<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMailAttachment extends Model
{
    protected $fillable = [
        'admin_mail_message_id', 'admin_mail_reply_id', 'disk', 'path',
        'original_name', 'mime_type', 'size',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(AdminMailMessage::class, 'admin_mail_message_id');
    }

    public function reply(): BelongsTo
    {
        return $this->belongsTo(AdminMailReply::class, 'admin_mail_reply_id');
    }
}