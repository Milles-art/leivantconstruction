<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'name',
        'company',
        'phone',
        'email',
        'region',
        'site_location',
        'subject',
        'project_type',
        'project_stage',
        'budget_range',
        'timeline',
        'preferred_contact',
        'message',
        'status',
        'priority',
        'assigned_to',
        'response',
        'internal_notes',
        'responded_at',
        'follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'follow_up_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function projectRequest(): HasOne
    {
        return $this->hasOne(ProjectRequest::class);
    }
}

