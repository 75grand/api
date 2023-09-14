<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $casts = [
        'ratings_distribution' => 'array',
        'difficulty' => 'float',
        'rating' => 'float',
        'rating_count' => 'integer',
        'take_again_percent' => 'integer',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
