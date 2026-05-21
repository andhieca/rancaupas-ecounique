<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tourism extends Model
{
    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function getRatingCountAttribute()
    {
        return $this->ratings()->count();
    }
}
