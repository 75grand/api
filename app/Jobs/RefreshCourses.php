<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\DistRequirement;
use App\Models\Professor;
use App\Models\Subject;
use App\Models\Term;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
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
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $courses = $this->loadCourseData();

        foreach ($courses as &$course) {
            $building = $course['meetingsFaculty'][0]['meetingTime']['building'];
            $room = $course['meetingsFaculty'][0]['meetingTime']['room'];
            $location = $building && $room ? "$building $room" : null;

            $days = array_values(array_filter([
                empty($course['meetingsFaculty'][0]['meetingTime']['sunday']) ? false : 0,
                empty($course['meetingsFaculty'][0]['meetingTime']['monday']) ? false : 1,
                empty($course['meetingsFaculty'][0]['meetingTime']['tuesday']) ? false : 2,
                empty($course['meetingsFaculty'][0]['meetingTime']['wednesday']) ? false : 3,
                empty($course['meetingsFaculty'][0]['meetingTime']['thursday']) ? false : 4,
                empty($course['meetingsFaculty'][0]['meetingTime']['friday']) ? false : 5,
                empty($course['meetingsFaculty'][0]['meetingTime']['saturday']) ? false : 6,
            ]));

            $startTime = $course['meetingsFaculty'][0]['meetingTime']['beginTime'];
            $endTime = $course['meetingsFaculty'][0]['meetingTime']['endTime'];

            // Add colon to time, e.g. 1300 -> 13:00
            if ($startTime) {
                $startTime = substr_replace($startTime, ':', 2, 0);
            }
            if ($endTime) {
                $endTime = substr_replace($endTime, ':', 2, 0);
            }

            $courseModel = Course::updateOrCreate([
                'remote_id' => $course['id'],
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
                'professor_id' => $this->getProfessor($course['faculty'])->id ?? null,
                'days' => $days,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);

            foreach ($this->getDistRequirements($course) as $requirement) {
                $courseModel->distRequirements()->syncWithoutDetaching($requirement->id);
            }

            $course['model'] = $courseModel;
        }

        Log::info('scraping cross listings & labs');
        foreach ($courses as $course) {
            $courseModel = $course['model'];

            if ($course['crossList'] !== null) {
                $this->attachCrossListings(
                    $course['crossList'],
                    $courseModel,
                    $courses
                );
            }

            $isClass = $course['scheduleTypeDescription'] === 'Class';
            $hasLabs = $course['isSectionLinked'];
            $labsScraped = $courseModel->labs()->exists();

            if ($hasLabs && $isClass && ! $labsScraped) {
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

        while ($offset < $totalCourses) {
            $data = $client->get(
                'https://macadmsys.macalester.edu/StudentRegistrationSsb/ssb/searchResults/searchResults',
                [
                    'txt_term' => $this->term->code,
                    'pageMaxSize' => $pageSize,
                    'pageOffset' => $offset,
                ]
            )->json();

            $count = count($data['data']);
            Log::info("fetched $count courses");

            $totalCourses = $data['totalCount'];
            $courses = array_merge($courses, $data['data'] ?? []);
            $offset += $pageSize;
        }

        return $courses;
    }

    /**
     * @param  array  $faculty The raw `faculty` item from the course response
     *
     * @todo Cache these to prevent excessive queries?
     */
    private function getProfessor(array $faculty): ?Professor
    {
        if (empty($faculty[0]['displayName']) || empty($faculty[0]['emailAddress'])) {
            return null;
        }

        $name = $faculty[0]['displayName']; // e.g. Cantrell, Paul
        $name = explode(', ', $name);
        $name = implode(' ', array_reverse($name)); // Paul Cantrell

        return Professor::firstOrCreate([
            'name' => $name,
        ], [
            'email' => $faculty[0]['emailAddress'],
        ]);
    }

    /**
     * Parse and return the distribution requirements that this course fulfills
     *
     * @return DistRequirement[]
     */
    private function getDistRequirements(array $course): array
    {
        $distRequirements = [];

        foreach ($course['sectionAttributes'] as $requirement) {
            $distRequirements[] = DistRequirement::firstOrCreate([
                'code' => $requirement['code'],
            ], [
                'name' => deep_clean_string($requirement['description']),
            ]);
        }

        return $distRequirements;
    }

    /**
     * Connect cross-listed courses in the database
     */
    private function attachCrossListings(string $identifier, Course $course, array $courses)
    {
        $matches = array_filter($courses, fn ($c) => $c['crossList'] === $identifier);

        if (empty($matches)) {
            Log::error("could not find matching course for $course->name");

            return;
        }

        $matchIds = array_map(fn ($c) => $c['id'], $matches);

        $matchModels = Course::whereIn('remote_id', $matchIds)->get();
        $course->crossListings()->syncWithoutDetaching($matchModels);
    }

    /**
     * @param  string  $code e.g. "HIST"
     * @param  string  $name e.g. "History"
     *
     * @todo Cache these to prevent excessive queries?
     */
    private function getSubject(string $code, string $name): Subject
    {
        return Subject::firstOrCreate([
            'code' => $code,
        ], [
            'name' => $name,
        ]);
    }

    /**
     * Get a client with the propeper session state
     *
     * @see https://jennydaman.gitlab.io/nubanned/#studentregistrationssb-clickcontinue
     */
    private function getSessionClient(string $term): PendingRequest
    {
        $request = Http::asForm()->post('https://macadmsys.macalester.edu/StudentRegistrationSsb/ssb/term/search', ['term' => $term]);
        $client = new Client(['cookies' => $request->cookies()]);

        return Http::setClient($client);
    }
}
