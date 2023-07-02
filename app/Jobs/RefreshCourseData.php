<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\Term;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshCourseData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('refreshing terms');
        RefreshTerms::dispatchSync();

        Log::info('refreshing courses');
        Term::where('name', 'like', 'Spring%')
            ->orWhere('name', 'like', 'Fall%')
            ->orderBy('code', 'desc')
            ->limit(2)
            ->get()->each(function($term) {
                RefreshCourses::dispatch($term);
                RefreshCourseDescriptions::dispatch($term);
            });

        Log::info('scraping prerequisites');
        Course::whereNull('prerequisites')
            ->get()->each(function($course) {
                ScrapePrerequisites::dispatch($course);
            });
    }
}
