<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class ListEventAttendees extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CalendarEvent $event)
    {
        return $event->users()->select(['id', 'avatar'])->get();
    }
}
