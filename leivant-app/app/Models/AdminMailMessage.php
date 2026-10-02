<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminMailMessage extends Model
{
    protected $fillable = [
        'account_id', 'uid', 'message_id', 'direction', 'mailbox', 'folder',
        'from_email', 'from_name', 'to_email', 'cc_email', 'bcc_email',
        'subject', 'body_text', 'body_html', 'received_at', 'sent_at',
        'is_read', 'is_draft', 'is_starred', 'status', 'assigned_to_user_id',
        'replied_at', 'parent_id', 'has_attachments', 'flags_json',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
            'sent_at' => 'datetime',
            'replied_at' => 'datetime',
            'is_read' => 'boolean',
            'is_draft' => 'boolean',
            'is_starred' => 'boolean',
            'has_attachments' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AdminMailAccount::class, 'account_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(AdminMailReply::class)->latest('sent_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AdminMailAttachment::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}