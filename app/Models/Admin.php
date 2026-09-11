<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'profile_image',
        'role',
        'status',
        'is_deleted',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'role' => 'integer',
            'status' => 'integer',
            'is_deleted' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reviewedDeposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'reviewed_by');
    }

    public function verifiedMembershipApplications(): HasMany
    {
        return $this->hasMany(MembershipApplication::class, 'verified_by');
    }

    public function approvedMembershipApplications(): HasMany
    {
        return $this->hasMany(MembershipApplication::class, 'approved_by');
    }

    public function approvedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'approved_by');
    }

    public function rejectedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'rejected_by');
    }

    public function processedRefunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'processed_by');
    }

    public function reviewedReturns(): HasMany
    {
        return $this->hasMany(ProductReturn::class, 'reviewed_by');
    }

    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class, 'user_id')
            ->where('user_type', 'admin');
    }
}
