<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function index(Request $request)
    {
        return CalendarEvent::select([
                'id',
                'title', 'location', 'description',
                'start_date', 'end_date',
                'calendar_name', 'image_url', 'url'
            ])
            ->whereDate('end_date', '>=', now())
            ->orderBy('start_date')
            ->get();
    }

    public function show(CalendarEvent $event, Request $request)
    {
        return $event->only([
            'id',
            'title', 'location', 'description',
            'start_date', 'end_date',
            'calendar_name', 'image_url', 'url'
        ]);
    }
}
