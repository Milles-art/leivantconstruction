<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'construction_phase_id',
        'name',
        'slug',
        'unit',
        'formula_key',
        'waste_factor',
        'source_hint',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'waste_factor' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ConstructionPhase::class, 'construction_phase_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(MaterialPrice::class);
    }
}
