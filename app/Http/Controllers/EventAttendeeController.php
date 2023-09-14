<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class EventAttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CalendarEvent $event)
    {
        return $event->users()->select(['id', 'avatar'])->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CalendarEvent $event, Request $request)
    {
        $data = $request->validate(['attending' => 'required|boolean']);

        if ($data['attending']) {
            $request->user()->events()->syncWithoutDetaching($event);
        } else {
            $request->user()->events()->detach($event);
        }

        return $this->index($event);
    }
}
