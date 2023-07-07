<?php

namespace App\Http\Controllers;

use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class ListEvents extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $events = CalendarEvent::query()
            ->whereDate('end_date', '>=', now())
            ->orderBy('start_date')
            ->withCount('users')
            ->get();

        return CalendarEventResource::collection($events);
    }
}
