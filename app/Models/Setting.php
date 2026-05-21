<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
