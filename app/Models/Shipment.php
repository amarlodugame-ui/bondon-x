<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'courier_id',
        'courier_name',
        'tracking_number',
        'status',
        'delivery_otp',
        'otp_verified_at',
        'shipped_at',
        'delivered_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'otp_verified_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}
