<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use ICal\Event as ICalEvent;
use ICal\ICal;
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
        $url = $request->user()->moodle_url;
        abort_if($url === null, 400);

        return cache()->remember(__METHOD__.$url, now()->addMinutes(5), function () use ($url, $request) {
            $data = Http::get($url)->body();

            if (Str::contains($data, 'Invalid authentication', true)) {
                $request->user()->update([
                    'moodle_user_id' => null,
                    'moodle_token' => null,
                ]);

                return null;
            }

            $cal = new ICal(options: ['filterDaysAfter' => 14]);
            $cal->initString($data);

            $events = $cal->events();
            $timeZone = $cal->calendarTimeZone();

            return collect($events)->map(function (ICalEvent $event) use ($timeZone) {
                return [
                    'id' => explode('@', $event->uid)[0],
                    'title' => Str::replaceLast(' is due', '', $event->summary),
                    'due' => Carbon::parse($event->dtstart, $timeZone)->format('c'),
                    'class' => self::formatClass($event->categories),
                ];
            })->toArray();
        }) ?? abort(400);
    }

    private static function formatClass(string $class): string
    {
        // Match a separated group of three numbers
        $number = Str::match('/(?:\D|^)(\d{3})(?:\D|$)/i', $class);
        if (! $number) {
            return $class;
        }

        // Match a separated group of four letters
        $department = Str::match('/(?:[^a-z]|^)([a-z]{4})(?:[^a-z]|$)/i', $class);
        if (! $department) {
            return $class;
        }
        $department = Str::upper($department);

        return "$department $number";
    }
}
