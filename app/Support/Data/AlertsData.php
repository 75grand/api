<?php

namespace App\Support\Data;

use Illuminate\Support\Facades\Http;

class AlertsData
{
    public static function get($fresh = false)
    {
        if($fresh) cache()->forget(__METHOD__);

        return cache()->remember(__METHOD__, now()->addMinutes(5), function() {
            $xml = Http::get('https://www.getrave.com/rss/macalester/channel1')->body();
            $xml = simplexml_load_string($xml);

            $text = str_replace([
                'Macalester Urgent Alert: ',
                ' www.macalester.edu/alert'
            ], '', $xml->channel->item->description);

            if(time() - strtotime($xml->channel->item->pubDate) < 60*2) {
                return $text;
            }

            return false;
        });
    }
}