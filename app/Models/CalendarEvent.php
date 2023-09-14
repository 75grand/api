<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'checked_for_data' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function formatDuration(): string
    {
        $startDate = $this->start_date->setTimezone('America/Chicago');
        $endDate = $this->end_date->setTimezone('America/Chicago');

        $diff = $endDate->diffInHours($startDate);
        if ($diff === 24) {
            return 'All Day';
        }

        $startDateString = $startDate->format('g:i A');
        if ($diff === 0) {
            return $startDateString;
        }

        $endDateString = $endDate->format('g:i A');

        return $startDateString.' – '.$endDateString;
    }
}
