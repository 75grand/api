<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $events = CalendarEvent::query()
            ->where(
                fn(Builder $query) => $query
                    ->whereBetween('start_date', [
                        now()->roundMinute(),
                        now()->roundMinute()->addMinute()->subMicro()
                    ])
                    ->orWhereBetween('start_date', [
                        now()->roundMinute()->addMinutes(15),
                        now()->roundMinute()->addMinutes(15+1)->subMicro()
                    ])
            )
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
                $event->location ? " ({$event->location})" : ''
            );

            send_expo_notification(
                to: $tokens,
                title: $event->title,
                body: $body,
                data: ['url' => "grand://calendar/$event->id"]
            );
        });
    }
}
