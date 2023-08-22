<?php

namespace App\Console;

use App\Jobs\RefreshCalendars;
use App\Jobs\RefreshCourseData;
use App\Jobs\ResetMoodleIntegration;
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
        $schedule->job(new RefreshCourseData)->dailyAt('4:00')->sentryMonitor('refresh-course-data');
        
        $schedule->job(new ResetMoodleIntegration)->yearlyOn(1, 1, '0:0');
        $schedule->job(new ResetMoodleIntegration)->yearlyOn(6, 1, '0:0');

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
