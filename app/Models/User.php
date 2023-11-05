<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $hidden = [
        'expo_token',
        'pivot'
    ];

    protected $casts = [
        'marketplace_ban' => 'boolean',
        'moodle_user_id' => 'integer'
    ];

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

    /**
     * Gets the Moodle iCal URL for this user. Use as a computed
     * attribute, i.e. `$user->moodle_url`.
     */
    public function getMoodleUrlAttribute(): ?string
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

    public function tasks()
    {
        return $this->hasMany(MoodleTask::class);
    }
}
