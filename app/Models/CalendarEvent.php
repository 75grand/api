<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'checked_for_image' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];
}
