<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoodleTaskResource;
use Illuminate\Http\Request;

class MoodleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $url = $request->user()->moodle_url;
        abort_if($url === null, 400);
        return MoodleTaskResource::collection($request->user()->tasks);
    }
}
