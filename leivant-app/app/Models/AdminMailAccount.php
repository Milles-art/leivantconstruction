<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class AdminMailAccount extends Model
{
    protected $fillable = [
        'user_id', 'email', 'display_name', 'imap_host', 'imap_port', 'imap_encryption',
        'smtp_host', 'smtp_port', 'smtp_encryption', 'username', 'encrypted_password',
        'folders_json', 'signature_html', 'is_active', 'last_synced_at', 'last_sync_error',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AdminMailMessage::class, 'account_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(AdminMailContact::class, 'account_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(AdminMailRule::class, 'account_id');
    }

    public function setPlainPassword(string $password): void
    {
        $this->encrypted_password = Crypt::encryptString($password);
    }

    public function plainPassword(): string
    {
        return Crypt::decryptString($this->encrypted_password);
    }

    public function folders(): array
    {
        $folders = json_decode((string) $this->folders_json, true) ?: [];

        return array_merge(static::defaultFolders(), $folders);
    }

    public function folderFor(string $key): string
    {
        return $this->folders()[$key] ?? static::defaultFolders()[$key] ?? 'INBOX';
    }

    public static function defaultFolders(): array
    {
        return [
            'inbox' => 'INBOX',
            'sent' => 'INBOX.Sent',
            'drafts' => 'INBOX.Drafts',
            'trash' => 'INBOX.Trash',
            'archive' => 'INBOX.Archive',
            'spam' => 'INBOX.Junk',
        ];
    }
}