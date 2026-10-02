<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMailRule extends Model
{
    protected $fillable = [
        'account_id', 'name', 'field', 'operator', 'value', 'action',
        'target_folder', 'assign_user_id', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AdminMailAccount::class, 'account_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_user_id');
    }
}