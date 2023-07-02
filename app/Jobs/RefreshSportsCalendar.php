<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RefreshSportsCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $feed = Http::get('https://athletics.macalester.edu/calendar.ashx/calendar.rss');
        $feed = simplexml_load_string($feed);

        foreach($feed->channel->item as $item) {
            $ev = $item->children('ev', true); // Event information
            $s = $item->children('s', true); // Team information

            CalendarEvent::updateOrCreate([
                'remote_id' => $item->guid
            ], [
                'title' => deep_clean_string(strstr($item->description, '\n', true)),
                'location' => $ev->location ?? null,
                'start_date' => Carbon::parse($ev->startdate),
                'end_date' => Carbon::parse($ev->enddate),
                'calendar_name' => 'Sports',
                'image_url' => $s->opponentlogo ?? null
            ]);
        }
    }
}
