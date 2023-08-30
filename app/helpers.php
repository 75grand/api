<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

if(!function_exists('title_case')) {
    function title_case(string $text): string {
        $words = explode(' ', $text); // Kaboom!
    
        foreach($words as &$word) {
            $wordLower = strtolower($word);
    
            // Skip words with special capitlization (e.g. DeWitt, MAX)
            // https://stackoverflow.com/a/25774229/
            if(strlen($word) - similar_text($word, $wordLower) > 1) continue;
    
             // Skip small words
            if(in_array($word, [
                'and', 'or', 'the', 'of', 'a', 'in',
                'to', 'as', 'for', 'nor', 'but'
            ])) continue;
    
            $word = ucfirst($word);
        }
    
        $text = implode(' ', $words);
        $text = ucfirst($text); // In case string starts with small word
        return $text;
    }
}

if(!function_exists('image_cdn_url')) {
    function image_cdn_url(string $imageUrl, $width = 0, $height = 0, $quality = 80, $trim = 0): string {
        if( // Short-circuit if URL is relative or local
            str_starts_with($imageUrl, '/') ||
            in_array(parse_url($imageUrl, PHP_URL_HOST), ['localhost', '127.0.0.1'])
        ) return $imageUrl;
    
        return 'https://wsrv.nl/?' . http_build_query([
            'url' => $imageUrl,
            'w' => $width,
            'h' => $height,
            'fit' => 'cover',
            'q' => $quality,
            'trim' => $trim,
            'output' => 'webp'
        ], encoding_type: PHP_QUERY_RFC3986);
    }
}

if(!function_exists('webhook_alert')) {
    function webhook_alert(string $title, array $fields = [], string $thumbnail = '') {
        if(!env('DISCORD_WEBHOOK') || app()->isLocal()) return;
    
        foreach($fields as $key => &$value) {
            $value = [
                'name' => $key,
                'value' => $value
            ];
        }
    
        Http::post(env('DISCORD_WEBHOOK'), [
            'embeds' => [
                [
                    'title' => $title,
                    'thumbnail' => ['url' => $thumbnail],
                    'fields' => array_values($fields)
                ]
            ]
        ]);
    }
}

if(!function_exists('deep_clean_string')) {
    function deep_clean_string(string $string, bool $stripTags = false): string {
        $string = html_entity_decode($string, ENT_QUOTES|ENT_HTML5, 'UTF-8');
        $string = html_entity_decode($string, ENT_QUOTES|ENT_HTML5, 'UTF-8');
        $string = trim($string);
        if($stripTags) $string = strip_tags($string);
        return $string;
    }
}