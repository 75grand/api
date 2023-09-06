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

class RefreshPresenceCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $info = Http::get('https://api.presence.io/macalester/v1/app/campus')->json();
        $events = Http::get('https://api.presence.io/macalester/v1/events')->json();
        
        foreach($events as $event) {
            if($event['hasCoverImage']) {
                $imageUrl = sprintf(
                    '%s/event-photos/%s/%s',
                    $info['cdn'], // e.g. https://macalester-cdn.presence.io
                    $info['apiId'], // e.g. 50ffbdc9-e8ef-4ef1-a6de-1d2c82f7b07a
                    $event['photoUriWithVersion'] // e.g. 947d5581-00af-4c8a-9f3f-28c51e68ef73.jpeg?v=0
                );

                $imageUrl = image_cdn_url($imageUrl, width: 700);
            }

            CalendarEvent::updateOrCreate([
                'remote_id' => $event['eventNoSqlId']
            ], [
                'title' => deep_clean_string($event['eventName']),
                'description' => empty($event['description']) ? null : deep_clean_string($event['description'], true),
                'location' => $event['location'] ?? null,
                'start_date' => Carbon::parse($event['startDateTimeUtc']),
                'end_date' => Carbon::parse($event['endDateTimeUtc']),
                'calendar_name' => 'Clubs',
                'url' => $info['portalLink'] . 'event/' . $event['uri'],
                'image_url' => $imageUrl ?? null
            ]);
        }
    }
}
