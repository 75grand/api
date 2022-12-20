<?php

namespace App\Support\Data;

use ICal\ICal;

class CalendarData
{
    public static function get($fresh = false)
    {
        if($fresh) cache()->forget(__METHOD__);

        return cache()->remember(__METHOD__, now()->addHours(6), function() {
            $calendars = [
                'Clubs' => 'https://calendar.google.com/calendar/ical/uak49d5n6hmg87onlafliquagq621es4%40import.calendar.google.com/public/basic.ics',
                'Sports' => 'https://calendar.google.com/calendar/ical/184a3fl8g2kgctprchksv9ohoev4csm3%40import.calendar.google.com/public/basic.ics',
                'Lectures' => 'https://calendar.google.com/calendar/ical/rgcupookhah3fr2uq5lbemckof8upsfo%40import.calendar.google.com/public/basic.ics',
                'Arts' => 'https://calendar.google.com/calendar/ical/287oc73evs3aaodd897kmkfv83lh4ukb%40import.calendar.google.com/public/basic.ics',
                'Featured' => 'https://calendar.google.com/calendar/ical/1umva68vh7qjhvpm0ua1dje051h34q9c%40import.calendar.google.com/public/basic.ics',
                'Campus' => 'https://calendar.google.com/calendar/ical/9247mqjnbg08hthcfqe0ebmusi0k7ohf%40import.calendar.google.com/public/basic.ics',
                'Career' => 'https://calendar.google.com/calendar/ical/uv4vv7rnmoulifk9989ftnoooigdq4ev%40import.calendar.google.com/public/basic.ics',
                'Dev Garden' => 'https://calendar.google.com/calendar/ical/macalester.edu_foee38ec77nqatr9hor7id17bk%40group.calendar.google.com/public/basic.ics',
                'Program Board' => 'https://calendar.google.com/calendar/ical/macalester.edu_mapq50fqbvln58l7m4mkg1ch1k%40group.calendar.google.com/public/basic.ics'
            ];

            $all_events = [];

            foreach($calendars as $calendar_name => $calendar_url) {
                $calendar = new ICal($calendar_url);
                $events = $calendar->eventsFromInterval('6 months');

                foreach($events as $event) {
                    $event_id = explode('?id=', $event->url ?? '')[1] ?? false;
                    $image = $event_id ? route('calendar.image', ['id' => $event_id]) : false;
                    $event_hash = md5($event->uid ?? $event->summary);

                    $formatted_event = [
                        'id' => $event_hash,
                        'title' => $event->summary,
                        'description' => $event->description ?? '',
                        'start_date' => date_create($event->dtstart)->format('c'),
                        'end_date' => date_create($event->dtend)->format('c'),
                        'is_all_day' => strtotime($event->dtend) - strtotime($event->dtstart) === 60*60*24,
                        'calendar' => $calendar_name,
                        'location' => $event->location ?? '',
                        'url' => $event->url ?? '',
                        'thumb_url' => $image ? image_cdn_url($image, 300, 300) : '',
                        'image_url' => $image ? image_cdn_url($image) : ''
                    ];

                    $all_events[$event_hash] = $formatted_event;
                }
            }

            // Sort date descending, i.e. earliest events first
            usort($all_events, fn($a, $b) => $a['start_date'] > $b['start_date']);

            return array_values($all_events);
        });
    }
}