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
        return cache()->remember(__METHOD__, now()->addHours(12), function() {
            $calendars = [
                'https://calendar.google.com/calendar/ical/181235a736929aeeeff4459188a0ff4becaa3ca4bea67aeb82b1d2a15b179006%40group.calendar.google.com/public/basic.ics',
                'https://calendar.google.com/calendar/ical/5a09b236bff5e9f6636548318d9c5abdc0a29fa5572a585ddba1b68db9a781df%40group.calendar.google.com/public/basic.ics'
            ];

            $results = [];

            foreach($calendars as $calendarUrl) {
                $calendar = new ICal($calendarUrl);
                $events = $calendar->eventsFromInterval('1 week');
    
                $result = [];
    
                foreach($events as $event) {
                    $name = $event->summary;
                    $customTexts = explode(':', $event->description);
                    $result[$name] = $result[$name] ?? ['name' => $name, 'events' => array()];
    
                    $result[$name]['events'][] = [
                        'text_before_start' => @$customTexts[0] ?: 'opens',
                        'start_date' => date_create($event->dtstart)->format('c'),
                        'text_before_end' => @$customTexts[1] ?: 'closes',
                        'end_date' => date_create($event->dtend)->format('c')
                    ];
                }
    
                $results = array_merge(
                    $results,
                    array_values($result)
                );
            }

            return $results;
        });
    }
}
