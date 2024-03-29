<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use Carbon\Carbon;
use ICal\ICal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RefreshCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $calendarName,
        private string $calendarUrl
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $calendar = new ICal($this->calendarUrl);

        /** @var \ICal\Event[] */
        $events = $calendar->eventsFromInterval('3 months');
        $timeZone = $calendar->calendarTimeZone();

        foreach($events as $event) {
            $savedEvent = CalendarEvent::updateOrCreate([
                'remote_id' => $event->uid
            ], [
                'title' => $this->clean($event->summary),
                'description' => $this->clean($event->description),
                'location' => $this->clean($event->location),
                'start_date' => Carbon::parse($event->dtstart, $timeZone)->format('c'),
                'end_date' => Carbon::parse($event->dtend, $timeZone)->format('c'),
                'calendar_name' => $this->calendarName,
                'url' => $this->clean($event->url)
            ]);

            $mightHaveData =
                !$savedEvent->checked_for_data &&
                $savedEvent->url &&
                Str::startsWith($savedEvent->url, 'https://www.macalester.edu/calendar/event/');

            ScrapeCalendarEventData::dispatchIf($mightHaveData, $savedEvent);
        }
    }

    private function clean(?string $string): ?string
    {
        if($string === null) return null;

        $string = html_entity_decode($string);
        $string = strip_tags($string);

        return $string;
    }
}
