<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'sku',
        'quantity',
        'unit_price',
        'total_price',
        'purchase_mode',
        'product_installment_plan_id',
        'installment_plan_name',
        'installment_total',
        'down_payment',
        'installment_amount',
        'installment_count',
        'interval_unit',
        'interval_value',
        'grace_days',
        'late_fee_type',
        'late_fee_value',
        'initial_payable',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'installment_total' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'installment_amount' => 'decimal:2',
            'installment_count' => 'integer',
            'interval_value' => 'integer',
            'grace_days' => 'integer',
            'late_fee_value' => 'decimal:2',
            'initial_payable' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function installmentPlan(): BelongsTo
    {
        return $this->belongsTo(ProductInstallmentPlan::class, 'product_installment_plan_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ProductReturn::class);
    }
}
