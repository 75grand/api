<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

enum NewsSource: string {
    case THE_MAC_WEEKLY = 'the-mac-weekly';
    case MACALESTER = 'macalester';
    case THE_HEGEMONOCLE = 'the-hegemonocle';
    case SUMMIT = 'summit';
    case REDDIT = 'reddit';

    public function getUrl(): string
    {
        return match($this) {
            NewsSource::MACALESTER => 'https://www.macalester.edu/news/wp-json/wp/v2/posts?per_page=5&_embed=wp:featuredmedia&_fields=date,link,title,rendered,_embedded,_links&categories=7',
            NewsSource::THE_MAC_WEEKLY =>    'https://themacweekly.com/wp-json/wp/v2/posts?per_page=5&_embed=wp:featuredmedia&_fields=date,link,title,rendered,_embedded,_links&categories=5271',
            NewsSource::THE_HEGEMONOCLE => 'https://issuu.com/call/profile/v1/documents/hegemonocle?limit=9',
            NewsSource::SUMMIT => 'https://www.macalestersummit.com/posts.json',
            NewsSource::REDDIT => 'https://api.reddit.com/r/macalester.json?limit=5'
        };
    }
}

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
        return cache()->remember("news-$source", now()->addHour(), function() use ($source) {
            $source = NewsSource::from($source);
            $url = $source->getUrl();
            $articles = Http::get($url)->json();

            return match($source) {
                NewsSource::MACALESTER => self::formatWordPressArticles($articles),
                NewsSource::THE_MAC_WEEKLY => self::formatWordPressArticles($articles),
                NewsSource::THE_HEGEMONOCLE => self::formatHegeArticles($articles),
                NewsSource::SUMMIT => self::formatSummitArticles($articles),
                NewsSource::REDDIT => self::formatRedditPosts($articles)
            };
        });
    }

    private static function formatRedditPosts(array $data): Collection
    {
        return collect($data['data']['children'])->map(function($post) {
            $data = $post['data'];

            return [
                'title' => deep_clean_string($data['title']),
                'date' => Carbon::createFromTimestamp($data['created']),
                'image_url' => null,
                'url' => $data['url'],
                'author' => $data['author'],
                'comments' => $data['num_comments'],
                'score' => $data['score'],
                'stickied' => $data['stickied']
            ];
        });
    }

    /**
     * Organizes articles from a standard WordPress.org site's REST API
     */
    private static function formatWordPressArticles(array $data): Collection
    {
        return collect($data)->transform(fn($article) => [
            'title' => strip_tags(html_entity_decode($article['title']['rendered'] ?? $article['title'])),
            'date' => date_create($article['date'])->format('c'), // I give up (https://core.trac.wordpress.org/ticket/41032)
            'image_url' =>
                $article['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['medium_large']['source_url']
                    ?? $article['_embedded']['wp:featuredmedia'][0]['source_url']
                    ?? null,
            'url' => $article['link']
        ]);
    }

    /**
     * Organizes articles from the Hegemonocle, which uses WordPress.com
     */
    private static function formatHegeArticles(array $data): Collection
    {
        return collect($data['items'])->transform(fn($article) => [
            'title' => strip_tags(html_entity_decode($article['title'])),
            'date' => Carbon::create(...$article['publishDate']),
            'image_url' => "https://image.isu.pub/{$article['documentId']}/jpg/page_1_thumb_large.jpg",
            'url' => 'https://issuu.com/' . $article['uri']
        ]);
    }

    private static function formatSummitArticles(array $data): Collection {
        return collect($data)->transform(fn($article) => [
            'title' => $article['title'],
            'date' => date_create($article['date'])->format('c'),
            'image_url' => $article['image'],
            'url' => $article['link']
        ]);
    }
}