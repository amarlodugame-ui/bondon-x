<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'charge',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'charge' => 'decimal:2',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function zoneRates(): HasMany
    {
        return $this->hasMany(ShippingZoneRate::class);
    }

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(
            ShippingZone::class,
            'shipping_zone_rates',
            'shipping_method_id',
            'shipping_zone_id'
        )->withPivot(['charge', 'status'])->withTimestamps();
    }
}
