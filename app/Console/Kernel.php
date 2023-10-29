<?php

namespace App\Console;

use App\Jobs\RefreshCalendars;
use App\Jobs\RefreshCourseData;
use App\Jobs\ResetMoodleIntegration;
use App\Jobs\SendEventNotifications;
use App\Jobs\SendMoodleNotifications;
use App\Jobs\SendStaleListingNotifications;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new RefreshCalendars)->hourlyAt(30)->sentryMonitor('refresh-calendars');
        $schedule->job(new RefreshCourseData)->dailyAt('5:00' /* 12:00 AM CST */)->sentryMonitor('refresh-course-data');

        $schedule->job(new SendEventNotifications)->everyMinute()->sentryMonitor('send-event-notifications');
        $schedule->job(new SendStaleListingNotifications)->dailyAt('23:00' /* 6:00 PM CST */)->sentryMonitor('send-stale-notifications');

        $schedule->command('app:refresh-moodle')->everySixHours();
        $schedule->job(new SendMoodleNotifications)->dailyAt('13:30' /* 8:30 AM CST */);

        $schedule->job(new ResetMoodleIntegration)->yearlyOn(1 /* January 1st */);
        $schedule->job(new ResetMoodleIntegration)->yearlyOn(6 /* June 1st */);

        // https://laravel.com/docs/10.x/telescope#data-pruning
        $schedule->command('telescope:prune')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
