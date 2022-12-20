<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MenuController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, string $date)
    {
        // Café Mac    = 159
        // The Grille  = 2108
        // Nessie’s    = 1014
        // Scotty’s    = 162
        // Atrium      = 161
        // Coffee Cart = 2109

        return cache()->remember($request->path(), now()->addHours(12), function() use ($date) {
            $cafeId = 159;
            $data = Http::get("https://legacy.cafebonappetit.com/api/1/cafe/$cafeId/date/$date")->json();

            $days = $data['cafe']['menu'][0]['days'];
            if(count($days) === 0) return false;

            $day = $days[0];
            $stations = $day['stations'];
            $meals = $day['dayParts'];
            $mealTypes = $data['mealTypes'];
            $corIcons = $data['corIcons'];
    
            $response = [];
    
            foreach($meals as $mealAbbr => $mealItems) {
                foreach($mealItems as &$mealItem) {
                    $mealItem = [
                        'id' => (int) $mealItem['item_id'],
                        'name' => title_case($mealItem['description']),
                        'station' => title_case($stations[$mealItem['station_id']]['station']),
                        'dietary_restrictions' => array_map(fn($id) => ([
                            'label' => $corIcons[$id]['type'],
                            'image_url' => $corIcons[$id]['image64Src'],
                            'description' => ucfirst(explode(': ', $corIcons[$id]['mouseover'], 2)[1])
                        ]), $mealItem['corIcons'] ?? [])
                    ];
                }
    
                foreach($mealTypes as $mealType) {
                    if($mealType['abbreviation'] === $mealAbbr) {
                        $response[$mealType['meal_type']] = $mealItems;
                    }
                }
            }
    
            return $response;
        }) ?: abort(404);
    }

    // public function item(Request $request, int $id)
    // {
    //     return cache()->rememberForever($request->path(), function() use ($id) {
    //         $data = Http::get("https://legacy.cafebonappetit.com/api/2/items?item=$id");
    //         $item = $data['items'][$id];

    //         $nutrition = [];
    //         foreach($item['nutrition_details'] as $detail) {
    //             $nutrition[$detail['label']] = implode(' ', [$detail['value'], $detail['unit']]);
    //         }

    //         return [
    //             'name' => title_case($item['label']),
    //             'nutrition' => $nutrition
    //         ];
    //     });
    // }
}
