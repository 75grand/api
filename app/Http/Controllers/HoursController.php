<?php

namespace App\Http\Controllers;

use ICal\ICal;
use Illuminate\Http\Request;

class HoursController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return cache()->remember('hours-calendar', now()->addHour(), function() {
            $calendar = new ICal('https://calendar.google.com/calendar/ical/181235a736929aeeeff4459188a0ff4becaa3ca4bea67aeb82b1d2a15b179006%40group.calendar.google.com/public/basic.ics');
            $events = $calendar->eventsFromInterval('1 week');

            $result = [];

            foreach($events as $event) {
                $name = $event->summary;
                $customTexts = explode(':', $event->description);
                $result[$name] = $result[$name] ?? ['name' => $name, 'events' => array()];

                $result[$name]['events'][] = [
                    'textBeforeStart' => @$customTexts[0] ?: 'opens',
                    'startDate' => date_create($event->dtstart)->format('c'),
                    'textBeforeEnd' => @$customTexts[1] ?: 'closes',
                    'endDate' => date_create($event->dtend)->format('c')
                ];
            }

            return array_values($result);
        });
    }
}
