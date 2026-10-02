<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConstructionPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'duration_factor',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'duration_factor' => 'decimal:2',
        ];
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
