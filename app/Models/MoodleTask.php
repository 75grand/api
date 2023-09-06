<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleTask extends Model
{
    use HasFactory;
    
    protected $casts = [
        'completed' => 'boolean',
        'sent_created_notification' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}