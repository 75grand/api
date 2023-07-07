<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class ShowEvent extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CalendarEvent $event, Request $request)
    {
        return $event->loadCount('users as attendees')->only([
            'id',
            'title', 'description',
            'location', 'latitude', 'longitude',
            'start_date', 'end_date',
            'calendar_name', 'image_url', 'url',
            'attendees'
        ]);
    }
}
