<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(string $source): Collection
    {
        return cache()->remember("news-$source", now()->addDay(), function() use ($source) {
            $source = NewsSource::from($source);
            $url = self::getUrl($source);
            $articles = Http::get($url)->json();
            
            return match($source) {
                NewsSource::MACALESTER => self::organizeWordPressArticles($articles),
                NewsSource::THE_MAC_WEEKLY => self::organizeMacWeeklyArticles($articles),
                NewsSource::THE_HEGEMONOCLE => self::organizeHegeArticles($articles),
                NewsSource::SUMMIT => self::organizeSummitArticles($articles)
            };
        });
    }

    /**
     * Organizes articles from The Mac Weekly, which requires a bit of customization
     */
    private static function organizeMacWeeklyArticles(array $data): Collection
    {
        return self::organizeWordPressArticles($data)->transform(function($article) {
            return $article;
        });
    }

    /**
     * Organizes articles from a standard WordPress.org site's REST API
     */
    private static function organizeWordPressArticles(array $data): Collection
    {
        return collect($data)->transform(fn($article) => [
            'title' => strip_tags(html_entity_decode($article['title']['rendered'] ?? $article['title'])),
            'date' => date_create($article['date'])->format('c'), // I give up (https://core.trac.wordpress.org/ticket/41032)
            'image_url' => $article['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['medium_large']['source_url'] ?? $article['_embedded']['wp:featuredmedia'][0]['source_url'],
            'url' => $article['link'] . '?utm_source=75grand'
        ]);
    }

    /**
     * Organizes articles from the Hegemonocle, which uses WordPress.com
     */
    private static function organizeHegeArticles(array $data): Collection
    {
        $data = $data['items'];
        return collect($data)->transform(fn($article) => [
            'title' => strip_tags(html_entity_decode($article['title'])),
            'date' => date_create($article['publishDate'])->format('c'), // I give up (https://core.trac.wordpress.org/ticket/41032)
            'image_url' => $article['coverUrl'],
            'url' => 'https://issuu.com/hegemonocle/docs/' . $article['uri']
        ]);
    }

    private static function organizeSummitArticles(array $data): Collection {
        return collect($data)->transform(fn($article) => [
            'title' => $article['title'],
            'date' => date_create($article['date'])->format('c'),
            'image_url' => $article['image'],
            'url' => $article['link']
        ]);
    }

    /**
     * Returns the API endpoint for a given source
     */
    private static function getUrl(NewsSource $source): string
    {
        return match($source) {
            NewsSource::MACALESTER => 'https://www.macalester.edu/news/wp-json/wp/v2/posts?per_page=5&_embed=wp:featuredmedia&_fields=date,link,title,rendered,_embedded,_links&categories=7',
            NewsSource::THE_MAC_WEEKLY => 'https://themacweekly.com/wp-json/wp/v2/posts?per_page=5&_embed=wp:featuredmedia&_fields=date,link,title,rendered,_embedded,_links&categories=5271',
            NewsSource::THE_HEGEMONOCLE => 'https://issuu.com/call/profile/v1/documents/hegemonocle?limit=8',
            NewsSource::SUMMIT => 'https://www.macalestersummit.com/posts.json'
        };
    }
}

enum NewsSource: string {
    case THE_MAC_WEEKLY = 'the-mac-weekly';
    case MACALESTER = 'macalester';
    case THE_HEGEMONOCLE = 'the-hegemonocle';
    case SUMMIT = 'summit';
}