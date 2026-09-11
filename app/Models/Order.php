<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'checkout_token',
        'order_no',
        'user_id',
        'payment_mode',
        'product_installment_plan_id',
        'subtotal',
        'cash_items_total',
        'installment_items_total',
        'initial_payable_amount',
        'discount_amount',
        'shipping_charge',
        'grand_total',
        'installment_total',
        'down_payment',
        'balance_used',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'order_status',
        'shipping_status',
        'shipping_method_id',
        'shipping_name',
        'shipping_mobile',
        'shipping_address',
        'shipping_area',
        'shipping_district',
        'courier_name',
        'tracking_number',
        'customer_note',
        'admin_note',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'reject_reason',
        'cancelled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'cash_items_total' => 'decimal:2',
            'installment_items_total' => 'decimal:2',
            'initial_payable_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'installment_total' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'balance_used' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productInstallmentPlan(): BelongsTo
    {
        return $this->belongsTo(ProductInstallmentPlan::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'rejected_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ProductReturn::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }
}
