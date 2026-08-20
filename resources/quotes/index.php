<?php

// Entry point for the quote list. Each status code has its own folder
// (404/, 403/, 419/, 429/, 500/, 503/, general/) — every .php file inside
// MUST return an array of quotes, and they all get merged automatically
// without needing to edit this file.
//
// "general/" is the fallback group used for any status code that doesn't
// have its own folder.
//
// Want to add quotes? Just drop a new file into the matching folder,
// e.g. resources/quotes/oops/403/source1.php — any filename works,
// the content is just:
//
//   <?php
//
//   return [
//       ['text' => '...', 'author' => '...', 'source' => null, 'lang' => 'id', 'genre' => 'humor'],
//   ];
//
// After publishing (php artisan vendor:publish --tag=oops-quotes), this
// folder lives at resources/quotes/oops/ in your app.

$loadGroup = static function (string $dir): array {
    $quotes = [];

    foreach (glob($dir.'/*.php') ?: [] as $file) {
        $quotes = array_merge($quotes, require $file);
    }

    return $quotes;
};

return [
    404 => $loadGroup(__DIR__.'/404'),
    403 => $loadGroup(__DIR__.'/403'),
    419 => $loadGroup(__DIR__.'/419'),
    429 => $loadGroup(__DIR__.'/429'),
    500 => $loadGroup(__DIR__.'/500'),
    503 => $loadGroup(__DIR__.'/503'),
    'general' => $loadGroup(__DIR__.'/general'),
];
