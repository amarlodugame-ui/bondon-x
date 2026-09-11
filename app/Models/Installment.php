<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Installment extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'installment_no',
        'due_date',
        'amount',
        'late_fee_amount',
        'balance_used',
        'paid_amount',
        'transaction_id',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'installment_no' => 'integer',
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'late_fee_amount' => 'decimal:2',
            'balance_used' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
