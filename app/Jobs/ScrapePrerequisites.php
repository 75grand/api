<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;

class ScrapePrerequisites implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Course $course
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = 'https://macadmsys.macalester.edu/StudentRegistrationSsb/ssb/searchResults/getSectionPrerequisites';

        $html = Http::asForm()->post($url, [
            'term' => $this->course->term->code,
            'courseReferenceNumber' => $this->course->crn
        ])->body();

        if(str_contains($html, 'No prerequisite information available.')) {
            $this->course->prerequisites = [];
        } else {
            $dom = HtmlDomParser::str_get_html($html);
            $this->rows = [...$dom->find('tbody > tr')];
            $this->rowIndex = 0;
    
            $this->course->prerequisites = $this->parse();
            Log::debug("scraped prerequisites for {$this->course->name}");
        }

        $this->course->save();
    }

    private array $rows = [];
    private int $rowIndex = 0;

    /**
     * @see https://www.chris-j.co.uk/parsing.php
     * @see https://github.com/sandboxnu/course-catalog-api/blob/master/scrapers/classes/parsersxe/prereqParser.ts#L67-L137
     */
    private function parse(): array
    {
        $values = [];
        $rule = 'AND';

        while($this->rowIndex < count($this->rows)) {
            $row = $this->rows[$this->rowIndex];
            $this->rowIndex++;

            $columns = $row->find('td');

            $columns = array_map(function($column) {
                $text = $column->innerHtml();
                return trim($text);
            }, [...$columns]);

            [$andOr, $openParen, $test, $score, $subjectName, $courseNumber, $level, $grade, $closeParen] = $columns;
            
            $subject = self::findSubject($subjectName);

            if($subject === null && $test === '') {
                // This often means that a department has been renamed
                // https://www.notion.so/jeromepaulos/63dc008b0986498486dc58b263b69b41?pvs=4#35f5b86976c141658e6dd62b2b3893b0
                Log::debug(
                    sprintf(
                        'subject not found: %s (prerequisite for crn=%s, term=%s)',
                        $subjectName, $this->course->crn, $this->course->term->code
                    )
                );
            }

            if($andOr) $rule = strtoupper($andOr);

            if($openParen) {
                $parsedValue = $this->parse();

                if($subject) {
                    $parsedValue['values'][] = [
                        'subject' => $subject->id,
                        'course_number' => $courseNumber
                    ];
                } else if($test) {
                    $parsedValue['values'][] = [
                        'test' => $test
                    ];
                }

                $values[] = $parsedValue;
            } else if($subject) {
                $values[] = [
                    'subject' => $subject->id,
                    'couse_number' => $courseNumber
                ];
            } else if($test) {
                $values[] = [
                    'test' => $test
                ];
            }

            if($closeParen) {
                return [
                    'rule' => $rule,
                    'values' => $values
                ];
            }
        }

        return [
            'rule' => $rule,
            'values' => $values
        ];
    }

    private static function findSubject(string $subjectName): ?Subject
    {
        $subjectName = match($subjectName) {
            'Humanities, Media, Cultural St' => 'Media and Cultural Studies',
            default => $subjectName
        };

        $subject = Subject::firstWhere('name', 'like', "$subjectName%");
        if($subject === null) return null;
        return $subject;
    }
}
