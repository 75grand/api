<?php

namespace App\Http\Controllers;

use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarEvent;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = CalendarEvent::query()
            ->whereDate('end_date', '>=', now('America/Chicago'))
            ->where('end_date', '!=', now('America/Chicago')->startOfDay())
            ->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) <= 24')
            ->orderBy('start_date')
            ->withCount('users')
            ->get();

        return CalendarEventResource::collection($events);
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $event)
    {
        $event->loadCount('users');

        return new CalendarEventResource($event);
    }

    /**
     * Display a basic page with the resource.
     */
    public function page(CalendarEvent $event)
    {
        return view('event', ['event' => $event]);
    }
}
