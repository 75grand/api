<?php

namespace App\Support\Data;

use Illuminate\Support\Facades\Http;

class MapData
{
    public static function get($fresh = false)
    {
        if($fresh) cache()->forget(__METHOD__);

        return cache()->remember(__METHOD__, now()->addHours(24), function() {
            $categories = implode(',', [
                '2556', // Academic and Administrative
                '2560', // Housing and Residence Halls
                '2557', // Athletic Facilities
                '2558', // Outdoor Spaces
                '2569', // Parking
                '9762', // All-Gender Restrooms
                // '9186', // Public Art
                '46009', // Restaurants
                '46010', // Coffee, Tea, Smoothies
                '46011', // Bakery
                '46027', // On-Campus
                '46049', // Grocery
                '9096' // Metro Transit
            ]);

            $data = Http::get("https://api.concept3d.com/categories/$categories?map=211&key=0001085cc708b9cef47080f064612ca5")->json();
            
            return array_map(function($layer) {
                return [
                    'name' => $layer['name'],
                    'marker_url' => 'https://assets.concept3d.com' . $layer['icon'],
                    'icon_url' => 'https://assets.concept3d.com' . $layer['listIcon'],
                    'markers' => array_map(function($marker) {
                        return [
                            'latitude' => $marker['lat'],
                            'longitude' => $marker['lng'],
                            'name' => $marker['name']
                        ];
                    }, $layer['children']['locations'])
                ];
            }, $data);
        });
    }
}