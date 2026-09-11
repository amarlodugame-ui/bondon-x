<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'site_name',
        'logo',
        'favicon',
        'address',
        'phone',
        'email',
        'cur_text',
        'cur_sym',
        'registration',
        'signup_bonus_amount',
        'order_prefix',
        'invoice_prefix',
        'cash_order_auto_approve',
        'installment_admin_approval',
        'membership_fee',
        'currency_format',
        'maintenance_mode',
        'maintenance_content',
    ];

    protected function casts(): array
    {
        return [
            'registration' => 'boolean',
            'signup_bonus_amount' => 'decimal:2',
            'cash_order_auto_approve' => 'boolean',
            'installment_admin_approval' => 'boolean',
            'membership_fee' => 'decimal:2',
            'currency_format' => 'integer',
            'maintenance_mode' => 'boolean',
        ];
    }

    /**
     * Backward-compatible helper for existing calls such as:
     * GeneralSetting::siteName('Dashboard')
     */
    public function scopeSiteName(Builder $query, ?string $pageTitle = null): string
    {
        $siteName = (string) ($query->value('site_name') ?? '');
        $pageTitle = trim((string) $pageTitle);

        return $pageTitle === '' ? $siteName : $siteName.' - '.$pageTitle;
    }
}
