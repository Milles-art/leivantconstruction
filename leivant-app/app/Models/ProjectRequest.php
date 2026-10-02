<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_template_id',
        'inquiry_id',
        'name',
        'phone',
        'email',
        'project_type',
        'house_type',
        'bedrooms',
        'bathrooms',
        'floors',
        'finish_level',
        'roof_type',
        'plot_size',
        'current_status',
        'work_type',
        'areas_to_modify',
        'budget',
        'location',
        'notes',
        'floor_area_sqm',
        'duration_weeks',
        'total_cost',
        'budget_status',
        'plan_payload',
        'brief_data',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'floors' => 'integer',
            'areas_to_modify' => 'array',
            'budget' => 'integer',
            'floor_area_sqm' => 'integer',
            'duration_weeks' => 'integer',
            'total_cost' => 'integer',
            'plan_payload' => 'array',
            'brief_data' => 'array',
        ];
    }

    public function houseTemplate(): BelongsTo
    {
        return $this->belongsTo(HouseTemplate::class);
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ProjectMaterial::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }
}

