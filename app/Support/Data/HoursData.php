<?php

namespace App\Support\Data;

use App\Models\Building;

class HoursData
{
    public static function get()
    {
        return Building::whereNull('parent_id')
            ->with('children')
            ->with(['events' => fn($q) => $q->orderBy('end')])
            ->get();
    }
}