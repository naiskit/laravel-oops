<?php

use Illuminate\Support\Facades\Route;

/*
 * Playground routes for `composer serve`. Triggers real exceptions so the
 * page goes through the actual exception handler, exactly like in an app.
 * ?theme=, ?tone= and ?lang= override .env for that one request.
 */

$statuses = [403, 404, 419, 429, 500, 503];
// Every bundled theme and tone (resources/views/themes, resources/lang/en/tones).
$themes = array_map(fn ($file) => basename($file, '.blade.php'), glob(__DIR__.'/../../resources/views/themes/*.blade.php'));
$tones = array_map(fn ($file) => basename($file, '.php'), glob(__DIR__.'/../../resources/lang/en/tones/*.php'));
$langs = ['en', 'id'];

Route::get('/', function () use ($statuses, $themes, $tones, $langs) {
    $sections = '';

    foreach ($themes as $theme) {
        $rows = '';

        foreach ($tones as $tone) {
            foreach ($langs as $lang) {
                $links = implode(' ', array_map(
                    fn ($status) => "<a href=\"/{$status}?theme={$theme}&tone={$tone}&lang={$lang}\">{$status}</a>",
                    $statuses
                ));
                $rows .= "<tr><td>{$tone}</td><td>{$lang}</td><td>{$links}</td></tr>";
            }
        }

        $sections .= "<h2>Theme: {$theme}</h2><table>{$rows}</table>";
    }

    return <<<HTML
        <!doctype html>
        <title>laravel-oops playground</title>
        <style>
            body { font-family: system-ui, sans-serif; margin: 40px; }
            td { padding: 6px 14px 6px 0; }
            a { margin-right: 10px; }
            h2 { margin-top: 32px; }
        </style>
        <h1>laravel-oops playground</h1>
        {$sections}
        <p>Also try <a href="/this-page-does-not-exist">a real missing page</a> (uses .env).</p>
        HTML;
});

Route::get('/{status}', function (int $status) {
    config(array_filter([
        'oops.theme' => request('theme'),
        'oops.tone' => request('tone'),
        'oops.lang' => request('lang'),
    ]));

    // A plain exception for 500, so it's the "unexpected error" path.
    if ($status === 500) {
        throw new RuntimeException('Playground: simulated server error.');
    }

    abort($status);
})->whereNumber('status');
