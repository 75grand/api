<?php

namespace App\Http\Controllers;

use App\Http\Resources\CalendarEventResource;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class ShowEvent extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CalendarEvent $event, Request $request)
    {
        $event->loadCount('users');
        return new CalendarEventResource($event);
    }
}
