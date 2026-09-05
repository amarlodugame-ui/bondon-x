<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'show_on_home',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'show_on_home' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];
}
