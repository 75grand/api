<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshCalendars implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const CALENDARS = [
        'Campus' => 'https://calendar.google.com/calendar/ical/9247mqjnbg08hthcfqe0ebmusi0k7ohf%40import.calendar.google.com/public/basic.ics',
        // 'Clubs' => 'https://calendar.google.com/calendar/ical/uak49d5n6hmg87onlafliquagq621es4%40import.calendar.google.com/public/basic.ics',
        // 'Sports' => 'https://calendar.google.com/calendar/ical/6leerv47ecvi8psddibiu0dqt3km72rt%40import.calendar.google.com/public/basic.ics',
        'Arts' => 'https://calendar.google.com/calendar/ical/287oc73evs3aaodd897kmkfv83lh4ukb%40import.calendar.google.com/public/basic.ics',
        'Career' => 'https://calendar.google.com/calendar/ical/uv4vv7rnmoulifk9989ftnoooigdq4ev%40import.calendar.google.com/public/basic.ics',
        'Lectures' => 'https://calendar.google.com/calendar/ical/rgcupookhah3fr2uq5lbemckof8upsfo%40import.calendar.google.com/public/basic.ics',
        'Dev Garden' => 'https://calendar.google.com/calendar/ical/macalester.edu_foee38ec77nqatr9hor7id17bk%40group.calendar.google.com/public/basic.ics',
        // 'Program Board' => 'https://calendar.google.com/calendar/ical/macalester.edu_mapq50fqbvln58l7m4mkg1ch1k%40group.calendar.google.com/public/basic.ics',
        'Featured' => 'https://calendar.google.com/calendar/ical/1umva68vh7qjhvpm0ua1dje051h34q9c%40import.calendar.google.com/public/basic.ics',
    ];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach (self::CALENDARS as $name => $url) {
            RefreshCalendar::dispatch($name, $url);
        }

        RefreshSportsCalendar::dispatch();
        RefreshPresenceCalendar::dispatch();
    }
}
