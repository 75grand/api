<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TransitController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return cache()->remember('transit', now()->addMinutes(5), function () {
            $stops = [
                'Grand & Snelling' => [
                    'Westbound' => [
                        'id' => 3089,
                        'line' => '63',
                        'color' => 'purple',
                    ],
                    'Eastbound' => [
                        'id' => 3114,
                        'line' => '63',
                        'color' => 'purple',
                    ],
                    'Southbound' => [
                        'id' => 17312,
                        'line' => 'METRO A',
                        'color' => 'red',
                    ],
                    'Northbound' => [
                        'id' => 17366,
                        'line' => 'METRO A',
                        'color' => 'red',
                    ],
                ],
                'Grand & Cambridge' => [
                    'Eastbound' => [
                        'id' => 3112,
                        'line' => '63',
                        'color' => 'purple',
                    ],
                    'Westbound' => [
                        'id' => 3091,
                        'line' => '63',
                        'color' => 'purple',
                    ],
                ],
                'Snelling & Saint Clair' => [
                    'Northbound' => [
                        'id' => 56116,
                        'line' => 'METRO A',
                        'color' => 'red',
                    ],
                    'Southbound' => [
                        'id' => 17318,
                        'line' => 'METRO A',
                        'color' => 'red',
                    ],
                ],
            ];

            $result = [];

            foreach ($stops as $stop_name => $stop) {
                $directions = array_map(function ($direction_name, $direction) {
                    $direction_data = Http::get("https://svc.metrotransit.org/nextrip/{$direction['id']}")->json();
                    $departures = array_map(function ($departure) {
                        return [
                            'is_live' => $departure['actual'] ?? false,
                            'date' => gmdate('c', $departure['departure_time']),
                        ];
                    }, $direction_data['departures']);

                    return [
                        'id' => $direction['id'],
                        'name' => $direction_name,
                        'line' => $direction['line'],
                        'line_color' => $direction['color'],
                        'latitude' => $direction_data['stops'][0]['latitude'],
                        'longitude' => $direction_data['stops'][0]['longitude'],
                        'departures' => $departures,
                    ];
                }, array_keys($stop), $stop);

                $result[] = [
                    'name' => $stop_name,
                    'directions' => $directions,
                ];
            }

            return $result;
        });
    }
}
