<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class ListEvents extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return CalendarEvent::query()
            ->select([
                'id',
                'title', 'description',
                'location', 'latitude', 'longitude',
                'start_date', 'end_date',
                'calendar_name', 'image_url', 'url'
            ])
            ->withCount('users as attendees')
            ->whereDate('end_date', '>=', now())
            ->orderBy('start_date')
            ->get();
    }
}
