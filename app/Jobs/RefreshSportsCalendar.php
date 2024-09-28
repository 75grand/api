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
use Illuminate\Support\Facades\Log;

class RefreshSportsCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::get('https://athletics.macalester.edu/calendar.ashx/calendar.rss')->body();
        $trimmedResponse = trim($response);

        try {
            $feed = simplexml_load_string($trimmedResponse);
        } catch (\Exception $e) {
            Log::error('Error parsing sports calendar XML: ' . $e->getMessage());
            return;
        }

        if ($feed === false) {
            Log::error('Failed to parse sports calendar XML');
            return;
        }

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
                'image_url' => image_cdn_url($s->opponentlogo, trim: 5) ?? null
            ]);
        }
    }
}
