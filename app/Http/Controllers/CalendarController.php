<?php

namespace App\Http\Controllers;

use App\Support\Data\CalendarData;
use App\Support\Data\CalendarImageData;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function list()
    {
        return CalendarData::get();
    }

    public function image(int $id)
    {
        return CalendarImageData::get($id);
    }
}
