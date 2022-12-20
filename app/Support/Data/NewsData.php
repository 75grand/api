<?php

namespace App\Support\Data;

use Illuminate\Support\Facades\Http;

class NewsData
{
    public static function get($fresh = false)
    {
        if($fresh) cache()->forget(__METHOD__);

        return cache()->remember(__METHOD__, now()->addHours(1), function() {
            return Http::get('https://www.macalestersummit.com/posts.json')->json();
        });
    }
}