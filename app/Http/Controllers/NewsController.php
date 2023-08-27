<?php

namespace App\Http\Controllers;

use App\Support\NewsSource;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function index()
    {
        $sources = [];

        foreach(NewsSource::cases() as $source) {
            $sources[$source->value] = [
                'name' => $source->getName(),
                'website' => $source->getWebsite(),
                'items' => $this->show($source->value)
            ];
        }

        return $sources;
    }

    public function show(string $source): Collection
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
            'url' => "https://issuu.com/{$article['uri']}/1?ff"
        ]);
    }

    private static function formatSummitArticles(array $data): Collection {
        return collect($data)->transform(fn($article) => [
            'title' => $article['title'],
            'date' => date_create($article['date'])->format('c'),
            'image_url' => $article['image'],
            'url' => $article['link']
        ])->take(5);
    }
}