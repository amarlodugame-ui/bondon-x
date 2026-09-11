<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentPlan extends Model
{
    protected $fillable = [
        'name',
        'interval_unit',
        'interval_value',
        'installment_count',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'interval_value' => 'integer',
            'installment_count' => 'integer',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function productInstallmentPlans(): HasMany
    {
        return $this->hasMany(ProductInstallmentPlan::class);
    }
}
