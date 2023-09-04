<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AlertController extends Controller
{
    /**
     * Checks the status of Macalester's online services
     * @return string[] The names of the services affected
     */
    public function getWebStatus(): array
    {
        return cache()->remember(__METHOD__, now()->addMinutes(5), function() {
            $data = Http::get('https://stats.uptimerobot.com/api/getMonitorList/L5qxzCPX9p')->json();

            return collect($data['psp']['monitors'])
                ->filter(fn($monitor) => $monitor['statusClass'] !== 'success')
                ->map(fn($monitor) => $monitor['name'])
                ->toArray();
        });
    }

    /**
     * Gets the most recent Macalester Urgent Alert
     * @return string|false The text of the alert, or false
     */
    public function getAlertStatus(): string|false
    {
        return cache()->remember(__METHOD__, now()->addMinutes(5), function() {
            $xml = Http::get('https://www.getrave.com/rss/macalester/channel1')->body();
            $xml = simplexml_load_string($xml);

            $text = Str::replace([
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
