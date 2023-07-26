<?php

namespace App\Http\Controllers;

use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = CalendarEvent::query()
            ->whereDate('end_date', '>=', now())
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
}
