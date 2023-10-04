<?php

namespace App\Jobs;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SendStaleListingNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $listings = Listing::query()
            ->where('available', true)
            ->whereDate('created_at', now()->subWeek())
            ->with('user')
            ->get();

        foreach($listings as $listing) {
            if($listing->user->expo_token === null) continue;

            $trimmedTitle = Str::limit($listing->title, 25);

            send_expo_notification(
                to: $listing->user->expo_token,
                title: 'Is this still for sale?',
                body: "It’s been a second since you posted “{$trimmedTitle}”. If it’s not available, you can update the listing.",
                data: ['url' => "grand://marketplace/$listing->id"]
            );
        }
    }
}
