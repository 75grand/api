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

        foreach($events as $event) {
            $savedEvent = CalendarEvent::updateOrCreate([
                'remote_id' => $event->uid
            ], [
                'title' => $this->clean($event->summary),
                'description' => $this->clean($event->description),
                'location' => $this->clean($event->location),
                'start_date' => Carbon::parse($event->dtstart),
                'end_date' => Carbon::parse($event->dtstart),
                'calendar_name' => $this->calendarName,
                'url' => $this->clean($event->url)
            ]);

            $mightHaveImage =
                !$savedEvent->checked_for_image &&
                $savedEvent->url &&
                str_starts_with($savedEvent->url, 'https://www.macalester.edu/calendar/event/');

            ScrapeCalendarEventImage::dispatchIf($mightHaveImage, $savedEvent);
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
