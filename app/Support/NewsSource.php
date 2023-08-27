<?php

namespace App\Support;

enum NewsSource: string
{
    case SUMMIT = 'summit';
    case THE_MAC_WEEKLY = 'the-mac-weekly';
    case THE_HEGEMONOCLE = 'the-hegemonocle';
    case MACALESTER = 'macalester';
    case REDDIT = 'reddit';

    public function getUrl(): string
    {
        return match($this) {
            self::SUMMIT => 'https://www.macalestersummit.com/posts.json',
            self::THE_MAC_WEEKLY =>    'https://themacweekly.com/wp-json/wp/v2/posts?per_page=5&_embed=wp:featuredmedia&_fields=date,link,title,rendered,_embedded,_links&categories=5271',
            self::THE_HEGEMONOCLE => 'https://issuu.com/call/profile/v1/documents/hegemonocle?limit=9',
            self::MACALESTER => 'https://www.macalester.edu/news/wp-json/wp/v2/posts?per_page=5&_embed=wp:featuredmedia&_fields=date,link,title,rendered,_embedded,_links',
            self::REDDIT => 'https://api.reddit.com/r/macalester.json?limit=5'
        };
    }

    public function getWebsite(): string
    {
        return match($this) {
            self::SUMMIT => 'https://www.macalestersummit.com/',
            self::THE_MAC_WEEKLY => 'https://themacweekly.com/',
            self::THE_HEGEMONOCLE => 'https://macalesterhegemonocle.wordpress.com/',
            self::MACALESTER => 'https://www.macalester.edu/news/',
            self::REDDIT => 'https://www.reddit.com/r/macalester'
        };
    }

    public function getName(): string
    {
        return match($this) {
            self::SUMMIT => 'Summit',
            self::THE_MAC_WEEKLY => 'The Mac Weekly',
            self::THE_HEGEMONOCLE => 'The Hegemonocle',
            self::MACALESTER => 'Macalester',
            self::REDDIT => 'r/Macalester'
        };
    }
}