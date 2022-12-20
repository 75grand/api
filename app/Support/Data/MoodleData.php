<?php

namespace App\Support\Data;

use ICal\Event;
use ICal\ICal;

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
        preg_match('/(?:\D|^)(\d{3})(?:\D|$)/i', $class, $number);
        if(count($number) !== 2) return $class;
        $number = $number[1];

        preg_match('/(?:[^a-z]|^)([a-z]{4})(?:[^a-z]|$)/i', $class, $department);
        if(count($department) !== 2) return $class;
        $department = strtoupper($department[1]);

        return "$department $number";
    }
}