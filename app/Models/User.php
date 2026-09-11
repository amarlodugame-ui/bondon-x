<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'member_no',
        'name',
        'balance',
        'saving_balance',
        'mobile',
        'is_member',
        'email',
        'password',
        'profile_image',
        'referral_code',
        'referred_by',
        'email_verified',
        'phone_verified',
        'status',
        'is_deleted',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'saving_balance' => 'decimal:2',
            'is_member' => 'boolean',
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'status' => 'integer',
            'is_deleted' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function membershipApplications(): HasMany
    {
        return $this->hasMany(MembershipApplication::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ProductReturn::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function commissionsReceived(): HasMany
    {
        return $this->hasMany(CommissionLog::class, 'to_id');
    }

    public function commissionsGenerated(): HasMany
    {
        return $this->hasMany(CommissionLog::class, 'from_id');
    }

    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class, 'user_id')
            ->where('user_type', 'user');
    }
}
