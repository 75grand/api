<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;

class ScrapeCalendarEventData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private CalendarEvent $event
    ) {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping)
                ->expireAfter(15) // 15 second timeout
                ->releaseAfter(1), // 1 second between request
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("scraping calendar data for {$this->event->title} ({$this->event->id})");
        $dom = HtmlDomParser::file_get_html($this->event->url);

        if ($image = $dom->findOneOrFalse('.context-image__image')) {
            $this->event->image_url = $image->getAttribute('src') ?: null;
        }

        if ($location = $dom->findOneOrFalse('#location-box')) {
            $this->event->latitude = $location->getAttribute('data-lat') ?: null;
            $this->event->longitude = $location->getAttribute('data-lon') ?: null;
        }

        if ($link = $dom->findOneOrFalse('.events-single-details__right .base-cta-featured')) {
            $this->event->url = $link->getAttribute('href') ?: null;
        }

        $this->event->checked_for_data = true;
        $this->event->save();
    }
}
