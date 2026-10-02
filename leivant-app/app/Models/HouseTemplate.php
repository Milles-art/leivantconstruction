<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HouseTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'house_type',
        'base_area_sqm',
        'area_per_bedroom_sqm',
        'floor_multiplier',
        'base_duration_weeks',
        'formulas',
        'model_config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_area_sqm' => 'integer',
            'area_per_bedroom_sqm' => 'integer',
            'floor_multiplier' => 'decimal:2',
            'base_duration_weeks' => 'integer',
            'formulas' => 'array',
            'model_config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function projectRequests(): HasMany
    {
        return $this->hasMany(ProjectRequest::class);
    }
}
