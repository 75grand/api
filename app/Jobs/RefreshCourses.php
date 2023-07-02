<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\DistRequirement;
use App\Models\Subject;
use App\Models\Term;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshCourses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Term $term
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $courses = $this->loadCourseData();

        foreach($courses as &$course) {
            $building = $course['meetingsFaculty'][0]['meetingTime']['building'];
            $room = $course['meetingsFaculty'][0]['meetingTime']['room'];
            $location = $building && $room ? "$building $room" : null;

            $professor = null;
            if(!empty($course['faculty'])) {
                $professor = $course['faculty'][0]['displayName']; // e.g. Cantrell, Paul
                $professor = explode(', ', $professor);
                $professor = implode(' ', array_reverse($professor)); // Paul Cantrell
            }

            $days = array_values(array_filter([
                empty($course['meetingsFaculty'][0]['meetingTime']['sunday']) ? false : 0,
                empty($course['meetingsFaculty'][0]['meetingTime']['monday']) ? false : 1,
                empty($course['meetingsFaculty'][0]['meetingTime']['tuesday']) ? false : 2,
                empty($course['meetingsFaculty'][0]['meetingTime']['wednesday']) ? false : 3,
                empty($course['meetingsFaculty'][0]['meetingTime']['thursday']) ? false : 4,
                empty($course['meetingsFaculty'][0]['meetingTime']['friday']) ? false : 5,
                empty($course['meetingsFaculty'][0]['meetingTime']['saturday']) ? false : 6
            ]));

            $startTime = $course['meetingsFaculty'][0]['meetingTime']['beginTime'];
            $endTime = $course['meetingsFaculty'][0]['meetingTime']['endTime'];

            // Add colon to time, e.g. 1300 -> 13:00
            if($startTime) $startTime = substr_replace($startTime, ':', 2, 0);
            if($endTime) $endTime = substr_replace($endTime, ':', 2, 0);

            $courseModel = Course::updateOrCreate([
                'remote_id' => $course['id']
            ], [
                'name' => deep_clean_string($course['courseTitle']),
                'crn' => $course['courseReferenceNumber'],
                'number' => $course['courseNumber'],
                'term_id' => $this->term->id,
                'subject_id' => $this->getSubject($course['subject'], $course['subjectDescription'])->id,
                'sequence_number' => $course['sequenceNumber'],
                'credits' => $course['creditHours'] ?? 0,
                'location' => $location,
                'max_enrollment' => $course['crossListCapacity'] ?? $course['maximumEnrollment'],
                'enrollment' => $course['crossListCount'] ?? $course['enrollment'],
                'professor' => $professor,
                'days' => $days,
                'start_time' => $startTime,
                'end_time' => $endTime
            ]);

            foreach($this->getDistRequirements($course) as $requirement) {
                $courseModel->distRequirements()->syncWithoutDetaching($requirement->id);
            }

            $course['model'] = $courseModel;
        }

        Log::info('scraping cross listings & labs');
        foreach($courses as $course) {
            $courseModel = $course['model'];

            if($course['crossList'] !== null) {
                $this->attachCrossListings(
                    $course['crossList'],
                    $courseModel,
                    $courses
                );
            }

            $isClass = $course['scheduleTypeDescription'] === 'Class';
            $hasLabs = $course['isSectionLinked'];
            $labsScraped = $courseModel->labs()->exists();

            if($hasLabs && $isClass && !$labsScraped) {
                ScrapeLabs::dispatch($courseModel);
            }
        }
    }

    private function loadCourseData(): array
    {
        $client = $this->getSessionClient($this->term->code);

        $courses = [];
        $pageSize = 500;
        $offset = 0;
        $totalCourses = 1;

        while($offset < $totalCourses) {
            $data = $client->get(
                'https://macadmsys.macalester.edu/StudentRegistrationSsb/ssb/searchResults/searchResults',
                [
                    'txt_term' => $this->term->code,
                    'pageMaxSize' => $pageSize,
                    'pageOffset' => $offset
                ]
            )->json();

            $count = count($data['data']);
            Log::info("fetched $count courses");

            file_put_contents(
                "/Users/jerome/Downloads/scrapes/{$this->term->code}-{$offset}.json",
                json_encode($data, JSON_PRETTY_PRINT)
            );

            $totalCourses = $data['totalCount'];
            $courses = array_merge($courses, $data['data'] ?? []);
            $offset += $pageSize;
        }

        return $courses;
    }

    /**
     * Parse and return the distRequirements that this course fulfills
     * @return DistRequirement[]
     */
    private function getDistRequirements(array $course): array
    {
        $distRequirements = [];

        foreach($course['sectionAttributes'] as $requirement) {
            $distRequirements[] = DistRequirement::firstOrCreate([
                'code' => $requirement['code']
            ], [
                'name' => $requirement['description']
           ]);
        }

        return $distRequirements;
    }

    /**
     * Connect cross-listed courses in the database
     */
    private function attachCrossListings(string $identifier, Course $course, array $courses)
    {
        $matches = array_filter($courses, fn($c) => $c['crossList'] === $identifier);

        if(empty($matches)) {
            Log::error("could not find matching course for $course->name");
            return;
        }

        $matchIds = array_map(fn($c) => $c['id'], $matches);

        $matchModels = Course::whereIn('remote_id', $matchIds)->get();
        $course->crossListings()->syncWithoutDetaching($matchModels);
    }

    /**
     * @param string $code e.g. "HIST"
     * @param string $name e.g. "History"
     * @todo Cache these to prevent excessive queries?
     */
    private function getSubject(string $code, string $name): Subject
    {
        return Subject::firstOrCreate([
            'code' => $code
        ], [
            'name' => $name
        ]);
    }

    /**
     * Get a client with the propeper session state
     * @see https://jennydaman.gitlab.io/nubanned/#studentregistrationssb-clickcontinue
     */
    private function getSessionClient(string $term): PendingRequest
    {
        $request = Http::asForm()->post('https://macadmsys.macalester.edu/StudentRegistrationSsb/ssb/term/search', ['term' => $term]);
        $client = new Client(['cookies' => $request->cookies()]);
        return Http::setClient($client);
    }
}
