<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'browser',
    ];
}
