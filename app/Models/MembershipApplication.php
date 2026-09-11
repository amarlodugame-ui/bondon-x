<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipApplication extends Model
{
    protected $fillable = [
        'user_id',
        'application_date',
        'name',
        'guardian',
        'district',
        'upazila',
        'union',
        'ward',
        'mobile',
        'face_photo',
        'nid_front',
        'nid_back',
        'member_signature',
        'declaration_date',
        'fee_amount',
        'fee_transaction_id',
        'status',
        'reject_reason',
        'verified_by',
        'verified_at',
        'comment',
        'approved_by',
        'approved_at',
        'approver_signature',
    ];

    protected function casts(): array
    {
        return [
            'application_date' => 'date',
            'declaration_date' => 'date',
            'fee_amount' => 'decimal:2',
            'status' => 'integer',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feeTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'fee_transaction_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
