<?php

namespace App\Http\Controllers;

use App\Support\Data\MapData;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function list()
    {
        return MapData::get();
    }
}
