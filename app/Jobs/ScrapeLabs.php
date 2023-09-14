<?php

namespace App\Jobs;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class ScrapeLabs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Course $course
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $html = Http::asForm()->post(
            'https://macadmsys.macalester.edu/StudentRegistrationSsb/ssb/searchResults/getLinkedSections',
            ['term' => $this->course->term->code, 'courseReferenceNumber' => $this->course->crn]
        )->body();

        $dom = HtmlDomParser::str_get_html($html);
        $courses = [...$dom->find('tbody > tr')];

        $crns = array_map(function ($course) {
            $columns = [...$course->find('td')];
            $crn = $columns[count($columns) - 1]->innerHtml();

            return trim($crn);
        }, $courses);

        $labs = $this->course->term->courses()->whereIn('crn', $crns)->get();
        $this->course->labs()->syncWithoutDetaching($labs);
    }
}
