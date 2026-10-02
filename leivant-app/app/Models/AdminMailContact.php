<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMailContact extends Model
{
    protected $fillable = ['account_id', 'email', 'name', 'source', 'last_seen_at'];

    protected function casts(): array
    {
        return ['last_seen_at' => 'datetime'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AdminMailAccount::class, 'account_id');
    }
}