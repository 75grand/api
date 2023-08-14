<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $hidden = ['expo_token', 'pivot'];

    public function referrer()
    {
        return $this->belongsTo(self::class, 'referrer_id');
    }

    public function referrals()
    {
        return $this->hasMany(self::class, 'referrer_id');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function events()
    {
        return $this->belongsToMany(CalendarEvent::class);
    }

    public function getMoodleUrl(): ?string
    {
        if($this->moodle_user_id && $this->moodle_token) {
            return
                'https://moodle.macalester.edu/calendar/export_execute.php' .
                '?userid=' . $this->moodle_user_id .
                '&authtoken=' . $this->moodle_token .
                '&preset_what=all&preset_time=recentupcoming';
        }

        return null;
    }
}
