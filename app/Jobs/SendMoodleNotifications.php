<?php

namespace App\Jobs;

use App\Models\MoodleTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendMoodleNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tasks = MoodleTask::query()
            ->whereNull('completed_at')
            ->where('due_date', '>', now())
            ->where(
                fn(Builder $query) => $query
                    ->whereDate('due_date', now())
                    ->orWhereDate('due_date', now()->tomorrow())
            )
            ->with('user')
            ->get();

        foreach($tasks as $task) {
            if($task->user->expo_token === null) continue;

            send_expo_notification(
                to: $task->user->expo_token,
                title: "$task->class assignment due soon",
                body: sprintf(
                    '“%s” is due in %s on %s',
                    Str::limit($task->title, 25),
                    $task->due_date->shortAbsoluteDiffForHumans(parts: 2),
                    $task->due_date->setTimezone('America/Chicago')->format('l \a\t g:i A')
                )
            );
        }
    }
}
