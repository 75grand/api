<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendEventNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $from = now()->roundMinute();
        $to = $from->copy()->addMinutes(15);

        $events = CalendarEvent::query()
            ->whereBetween('start_date', [$from, $to])
            ->whereHas('users')
            ->with('users')
            ->get();

        $events->each(function($event) {
            $tokens = $event->users->pluck('expo_token')->filter();
            if($tokens->count() === 0) return;

            $minsUntil = $event->start_date->diffInRealMinutes();
            $body = sprintf(
                'Starting %s%s',
                $minsUntil > 1 ? "in $minsUntil minutes" : 'now',
                $event->location ? " at {$event->location}" : ''
            );

            Http::withToken(env('EXPO_ACCESS_TOKEN'))
                ->post('https://exp.host/--/api/v2/push/send', [
                    'to' => $tokens,
                    'title' => $event->title,
                    'body' => $body,
                    'sound' => 'default',
                    'data' => ['url' => "grand://calendar/$event->id"]
                ]);
        });
    }
}
