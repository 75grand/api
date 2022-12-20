<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use voku\helper\HtmlDomParser;

class CalendarImageController extends Controller
{
    public function show(int $id)
    {
        // cache()->forget(__METHOD__ . $id);

        $url = cache()->rememberForever(__METHOD__ . $id, function() use ($id) {
            $dom = HtmlDomParser::file_get_html("https://www.macalester.edu/calendar/event/?id=$id");
            $image = $dom->findOneOrFalse('.context-image__image');
            if($image) return $image->getAttribute('src');
        });

        // Failed Dependency, probably should be a 404 but I'm feeling fancy
        abort_if(empty($url), 424);

        return redirect($url, Response::HTTP_PERMANENTLY_REDIRECT);
    }
}
