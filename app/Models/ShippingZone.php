<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'districts',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'districts' => 'array',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingZoneRate::class);
    }

    public function methods(): BelongsToMany
    {
        return $this->belongsToMany(
            ShippingMethod::class,
            'shipping_zone_rates',
            'shipping_zone_id',
            'shipping_method_id'
        )->withPivot(['charge', 'status'])->withTimestamps();
    }
}
