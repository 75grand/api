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
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;

class ScrapeCalendarEventImage implements ShouldQueue
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
        $image = $dom->findOneOrFalse('.context-image__image');

        if($image) {
            $this->event->image_url = $image->getAttribute('src');
        }

        $this->event->checked_for_image = true;
        $this->event->save();
    }
}
