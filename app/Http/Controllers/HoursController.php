<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use ICal\ICal;
use Illuminate\Http\Request;

class HoursController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return cache()->remember(__METHOD__, now()->addMinutes(30), function () {
            $calendars = [
                'https://calendar.google.com/calendar/ical/181235a736929aeeeff4459188a0ff4becaa3ca4bea67aeb82b1d2a15b179006%40group.calendar.google.com/public/basic.ics',
                'https://calendar.google.com/calendar/ical/5a09b236bff5e9f6636548318d9c5abdc0a29fa5572a585ddba1b68db9a781df%40group.calendar.google.com/public/basic.ics',
            ];

            $results = [];

            foreach ($calendars as $calendarUrl) {
                $calendar = new ICal($calendarUrl, [
                    'defaultSpan' => 1,
                    'filterDaysAfter' => 20,
                    'filterDaysBefore' => 2,
                    'httpUserAgent' => 'api@75grand.net',
                ]);

                $timeZone = $calendar->calendarTimeZone();

                $events = $calendar->eventsFromInterval('1 week');

                $result = [];

                foreach ($events as $event) {
                    $name = $event->summary;
                    $customTexts = explode(':', $event->description);
                    $result[$name] = $result[$name] ?? ['name' => $name, 'events' => []];

                    $result[$name]['events'][] = [
                        'text_before_start' => @$customTexts[0] ?: 'opens',
                        'start_date' => Carbon::parse($event->dtstart, $timeZone)->format('c'),
                        'text_before_end' => @$customTexts[1] ?: 'closes',
                        'end_date' => Carbon::parse($event->dtend, $timeZone)->format('c'),
                    ];
                }

                $results = array_merge(
                    $results,
                    array_values($result)
                );
            }

            usort($results, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return $results;
        });
    }
}
