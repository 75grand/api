<?php

function title_case(string $text): string {
    $text = strtolower($text);
    $words = explode(' ', $text); // Kaboom!

    foreach($words as &$word) {
        if(strtoupper($text) === $text) continue; // Skip acronyms
        if(in_array($word, [
            'and', 'or', 'the', 'of', 'a', 'in',
            'to', 'as', 'for', 'nor', 'but'
        ])) continue; // Skip small words
        $word = ucfirst($word);
    }

    $text = implode(' ', $words);
    $text = ucfirst($text); // In case string starts with small word
    return $text;
}

function image_cdn_url(string $image_url, $width = 0, $height = 0, $quality = 80): string {
    if( // Short-circuit if URL is relative or local
        str_starts_with($image_url, '/') ||
        in_array(parse_url($image_url, PHP_URL_HOST), ['localhost', '127.0.0.1'])
    ) return $image_url;

    return 'https://wsrv.nl/?' . http_build_query([
        'url' => $image_url,
        'w' => $width,
        'h' => $height,
        'fit' => 'cover',
        'q' => $quality
    ]);
}