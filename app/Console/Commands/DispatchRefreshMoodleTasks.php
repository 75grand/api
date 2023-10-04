<?php

namespace App\Console\Commands;

use App\Jobs\RefreshMoodleTasks;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class DispatchRefreshMoodleTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-moodle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to refresh all Moodle tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull(['moodle_token', 'moodle_user_id'])->get();

        $jobs = $users->map(function($user, $index) {
            $job = new RefreshMoodleTasks($user);
            $offset = now()->addSeconds($index * RefreshMoodleTasks::SECONDS_PER_JOB);
            return $job->delay($offset);
        });

        Bus::batch($jobs)
            ->name('Check for Moodle assignments')
            ->allowFailures()
            ->dispatch();
    }
}
