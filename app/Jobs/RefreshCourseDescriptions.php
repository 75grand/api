<?php

namespace App\Jobs;

use App\Models\Term;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RefreshCourseDescriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Term $term
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $courses = Http::acceptJson()->get(
            'https://macadmsys.macalester.edu/macssb/internalPb/virtualDomains.classScheduleClasses',
            [
                'parm_term' => $this->term->code,
                'param_term' => $this->term->code,
            ]
        )->json();

        foreach ($courses as $course) {
            $html = $course['DATA_HTML_TEXT'];
            preg_match('/TableCRN(\d+)/', $html, $matches);

            $this->term->courses()->where('crn', $matches[1])->update([
                'description' => $course['CATALOG_TEXT_LONG'],
                'attendance_required' => Str::contains($html, 'First day attendance required'),
            ]);
        }
    }
}
