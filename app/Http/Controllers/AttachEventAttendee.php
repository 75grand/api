<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class AttachEventAttendee extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CalendarEvent $event, Request $request)
    {
        $data = $request->validate(['attending' => 'required,boolean']);
        
        if($data['attending']) {
            $request->user()->events()->syncWithoutDetaching($event);
        } else {
            $request->user()->events()->detach($event);
        }
    }
}
