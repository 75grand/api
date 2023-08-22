<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use ICal\ICal;
use ICal\Event as ICalEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoodleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return [
            [
                'id' => '0',
                'title' => 'Project Code Submission',
                'due' => now()->addHours(16)->format('c'),
                'class' => 'COMP 128'
            ],
            [
                'id' => '1',
                'title' => 'HW 11',
                'due' => now()->subDays(1)->format('c'),
                'class' => 'MATH 279'
            ],
            [
                'id' => '2',
                'title' => 'Presentation Slides',
                'due' => now()->addHours(16)->format('c'),
                'class' => 'COMP 128'
            ]
        ];

        $url = $request->user()->moodle_url;
        abort_if($url === null, 400);

        return cache()->remember(__METHOD__.$url, now()->addMinutes(5), function() use ($url, $request) {
            $data = Http::get($url)->body();

            if(Str::contains($data, 'Invalid authentication', true)) {
                $request->user()->update([
                    'moodle_user_id' => null,
                    'moodle_token' => null
                ]);

                return false;
            }

            $cal = new ICal(options: ['initString' => $data]);
            $events = $cal->events();

            return collect($events)->map(function(ICalEvent $event) {
                return [
                    'id' => explode('@', $event->uid)[0],
                    'title' => Str::replaceLast(' is due', '', $event->summary),
                    'due' => Carbon::parse($event->dtstart)->format('c'),
                    'class' => self::formatClass($event->categories)
                ];
            })->toArray();
        }) ?: abort(400);
    }

    private static function formatClass(string $class): string
    {
        // Match a separated group of three numbers
        $number = Str::match('/(?:\D|^)(\d{3})(?:\D|$)/i', $class);
        if(!$number) return $class;
        $number = $number[1];

        // Match a separated group of four letters
        $department = Str::match('/(?:[^a-z]|^)([a-z]{4})(?:[^a-z]|$)/i', $class);
        if(!$department) return $class;
        $department = Str::upper($department[1]);

        return "$department $number";
    }
}
