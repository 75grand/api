<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use ICal\ICal;
use ICal\Event as ICalEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MoodleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $url = $request->user()->moodle_url;
        abort_if($url === null, 400);

        return cache()->remember(__METHOD__.$url, now()->addMinutes(5), function($url) {
            $cal = new ICal($url);
            $events = $cal->events();

            return collect($events)->map(function(ICalEvent $event) {
                return [
                    'id' => explode('@', $event->uid)[0],
                    'title' => Str::replaceLast(' is due', '', $event->summary),
                    'due' => Carbon::parse($event->dtstart)->format('c'),
                    'class' => self::formatClass($event->categories)
                ];
            })->toArray();
        });
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
