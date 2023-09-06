<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\IcalendarGenerator\Components\Calendar;
use Illuminate\Support\Str;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Components\Timezone;

class CalendarFeedController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $firstName = Str::before($user->name, ' ');

        $calendar = Calendar::create()
            ->name('75grand')
            ->description("$firstName's events saved from 75grand")
            ->refreshInterval(15)
            ->timezone(Timezone::create('America/Chicago'));

        foreach($user->events as $event) {
            $iCalEvent = Event::create()
                ->name($event->title)
                ->uniqueIdentifier($event->id)
                ->period($event->start_date, $event->end_date);

            if($event->description) $iCalEvent->description($event->description);
            if($event->latitude && $event->longitude) $iCalEvent->coordinates($event->latitude, $event->longitude);
            if($event->location) $iCalEvent->address($event->location);
            if($event->image_url) $iCalEvent->image($event->image_url);
            if($event->url) $iCalEvent->url($event->url);

            $calendar->event($iCalEvent);
        }

        return response($calendar->get(), headers: [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="events.ics"'
        ]);
    }
}
