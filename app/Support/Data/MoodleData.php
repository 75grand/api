<?php

namespace App\Support\Data;

use ICal\Event;
use ICal\ICal;
use Illuminate\Support\Str;

class MoodleData
{
    public static function get($fresh = false)
    {
        if($fresh) cache()->forget(__METHOD__);

        if(!auth()->check() || auth()->user()->getMoodleUrl() === null) {
            return null;
        }

        return cache()->remember(__METHOD__, now()->addMinutes(5), function() {
            $url = auth()->user()->getMoodleUrl();
            $cal = new ICal($url);
            $events = $cal->events();
            return collect($events)->map(function(Event $event) {
                return [
                    'id' => explode('@', $event->uid)[0],
                    'title' => str_replace_last(' is due', '', $event->summary),
                    'due' => date_create($event->dtstart)->format('c'),
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