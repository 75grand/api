<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class EventAttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CalendarEvent $calendarEvent)
    {
        return $calendarEvent->users()->select(['id', 'avatar'])->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $data = $request->validate(['attending' => 'required|boolean']);
        
        if($data['attending']) {
            $request->user()->events()->syncWithoutDetaching($calendarEvent);
        } else {
            $request->user()->events()->detach($calendarEvent);
        }

        return $this->index($calendarEvent);
    }
}
