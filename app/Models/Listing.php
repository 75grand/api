<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory;
    
    protected $casts = [
        'available' => 'boolean',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formatPrice()
    {
        if($this->price === 0) return 'Free';
        return '$' . number_format($this->price);
    }

    public function formatDistance()
    {
        if($this->miles_from_campus === 0) return 'On Campus';
        if($this->miles_from_campus >= 9) return '9+ miles from campus';
        return $this->miles_from_campus . ' ' . Str::plural('mile', $this->miles_from_campus) . ' from campus';
    }
}
