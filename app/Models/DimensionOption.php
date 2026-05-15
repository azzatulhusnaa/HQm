<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'label',
        'value',
        'width',
        'height',
        'base_price',
        'included_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'base_price' => 'decimal:2',
            'included_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
