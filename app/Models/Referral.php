<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'commission_type',
        'level',
        'type',
        'commission_value',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'commission_value' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
}
