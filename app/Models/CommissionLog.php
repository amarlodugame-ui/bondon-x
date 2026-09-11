<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionLog extends Model
{
    protected $fillable = [
        'to_id',
        'from_id',
        'level',
        'amount',
        'type',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'amount' => 'decimal:2',
        ];
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id');
    }
}
