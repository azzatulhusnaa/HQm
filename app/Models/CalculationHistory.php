<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'calculation_template_id',
        'template_name',
        'formula_type',
        'width',
        'height',
        'amount',
        'multiplier',
        'total',
        'breakdown',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'amount' => 'decimal:2',
            'multiplier' => 'decimal:6',
            'total' => 'decimal:2',
            'breakdown' => 'array',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CalculationTemplate::class, 'calculation_template_id');
    }
}
