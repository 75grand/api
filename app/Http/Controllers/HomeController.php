<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Support\Data\HoursData;
use App\Support\Data\MoodleData;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        // $links = Link::select(['id', 'name', 'category', 'icon', 'url', 'keywords'])->orderBy('order')->get();
        // $articles = (new NewsController)('summit')->take(4);

        return Inertia::render('Home', [
            'links' => fn() => Link::orderBy('order')->get(),
            'articles' => fn() => (new NewsController)('summit')->take(5),
            'moodle' => fn() => MoodleData::get(),
            'buildings' => fn() => HoursData::get()
            // 'moodle' => Inertia::lazy(MoodleData::get(...))
        ]);
    }
}