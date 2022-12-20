<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;

class LinkController extends Controller
{
    public static function list()
    {
        return Link::select(['id', 'name', 'category', 'web_icon AS webIcon', 'ios_icon AS iosIcon', 'url'])
            ->withCount([
                'clicks as popularity' => function(Builder $query) {
                    $date = Carbon::now()->subWeek();
                    $query->where('date', '>=', $date);
                }
            ])
            ->orderBy('order')
            ->get()->map(function($link) {
                $link['url'] = route('redirect', $link['id']);
                return $link;
            });
    }

    public static function redirect(Link $link)
    {
        $link->clicks()->create([
            'ip_address' => request()->header('HTTP_CF_CONNECTING_IP') ?? request()->ip(),
            'link_id' => $link->id,
            'user_id' => auth()->user()?->id
        ]);

        return redirect(to: $link->url);
    }
}
