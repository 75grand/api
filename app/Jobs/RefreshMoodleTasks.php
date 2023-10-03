<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use ICal\ICal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RefreshMoodleTasks implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user
    ) {}

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping)->expireAfter(5)
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = $this->user->moodle_url;
        $data = Http::get($url)->body();

        if(Str::contains($data, 'Invalid authentication', true)) {
            $this->user->update([
                'moodle_user_id' => null,
                'moodle_token' => null
            ]);

            return;
        }

        $cal = new ICal(options: ['filterDaysAfter' => 14]);
        $cal->initString($data);

        /** @var \ICal\Event[] */
        $events = $cal->events();
        $timeZone = $cal->calendarTimeZone();

        try {
            DB::beginTransaction();
    
            foreach($events as $event) {
                $this->user->tasks()->updateOrCreate([
                    'remote_id' => $event->uid
                ], [
                    'title' => Str::replaceLast(' is due', '', $event->summary),
                    'due_date' => Carbon::parse($event->dtstart, $timeZone),
                    'class' => $this->formatClass($event->categories),
                    'description' => $event->description
                ]);
            }
    
            DB::commit();
        } finally {
            DB::rollBack();
        }
    }

    private function formatClass(string $class): string
    {
        // Match a separated group of three numbers
        $number = Str::match('/(?:\D|^)(\d{3})(?:\D|$)/i', $class);
        if(!$number) return $class;

        // Match a separated group of four letters
        $department = Str::match('/(?:[^a-z]|^)([a-z]{4})(?:[^a-z]|$)/i', $class);
        if(!$department) return $class;
        $department = Str::upper($department);

        return "$department $number";
    }
}
