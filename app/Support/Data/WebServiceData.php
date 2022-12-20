<?php

namespace App\Support\Data;

use Illuminate\Support\Facades\Http;

class WebServiceData
{
    public static function get($fresh = false)
    {
        if($fresh) cache()->forget(__METHOD__);

        return cache()->remember(__METHOD__, now()->addMinutes(5), function() {
            $data = Http::get('https://stats.uptimerobot.com/api/getMonitorList/L5qxzCPX9p')->json();

            return collect($data['psp']['monitors'])
                ->filter(fn($monitor) => $monitor['statusClass'] !== 'success')
                ->map(fn($monitor) => $monitor['name'])
                ->toArray();
        });
    }
}