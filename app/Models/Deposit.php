<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $fillable = [
        'user_id',
        'payment_method_id',
        'wallet_type',
        'amount',
        'payer_mobile',
        'transaction_id',
        'screenshot',
        'status',
        'reject_reason',
        'reviewed_by',
        'reviewed_at',
        'approved_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function approvedTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'approved_transaction_id');
    }
}
