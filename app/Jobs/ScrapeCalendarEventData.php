<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use voku\helper\HtmlDomParser;

class ScrapeCalendarEventData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private CalendarEvent $event
    ) {}

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            // Scrape only one image at a time
            new WithoutOverlapping()
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dom = HtmlDomParser::file_get_html($this->event->url);

        if($image = $dom->findOneOrFalse('.context-image__image')) {
            $this->event->image_url = $image->getAttribute('src');
        }

        if($location = $dom->findOneOrFalse('#location-box')) {
            $this->event->latitude = $location->getAttribute('data-lat');
            $this->event->longitude = $location->getAttribute('data-lon');
        }

        $this->event->checked_for_image = true;
        $this->event->save();
    }
}
