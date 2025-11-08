<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyGuide extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];
}
