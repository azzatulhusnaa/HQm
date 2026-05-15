<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalculationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'formula_type',
        'description',
        'multiplier',
        'minimum_charge',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'multiplier' => 'decimal:6',
            'minimum_charge' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CalculationHistory::class);
    }
}
