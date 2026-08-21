<?php

return [

    // Turn off to fall back to Laravel's default error page.
    'enabled' => env('OOPS_ENABLED', true),

    // Show the friendly page even when APP_DEBUG=true.
    'force' => env('OOPS_FORCE', false),

    // HTTP status codes this package intercepts.
    'statuses' => [403, 404, 419, 429, 500, 503],

    // The view for each status code is resolved automatically as
    // "oops::{status}" (e.g. oops::404, oops::403), falling back to
    // "oops::general" when a status has no dedicated view. Publish tag
    // "oops-views" to edit any of them. Set this to a view name to force
    // that single view for every status instead — note this also switches
    // which status's default accent color applies (see "theme" below).
    'view' => null,

    // Language for the title, message, and "back home" button ("id" or
    // "en" — see "messages" and "ui" below). Leave null to follow the
    // app's own locale (config('app.locale')), falling back to "id" if
    // that locale has no translation here.
    // Via .env: OOPS_LOCALE=en
    'locale' => env('OOPS_LOCALE'),

    // Path to the app's own quote index (result of publishing tag
    // "oops-quotes", which creates a resources/quotes/oops/ folder with
    // index.php + one subfolder per status code). Falls back to the
    // package's bundled quotes when not published.
    'quotes_path' => resource_path('quotes/oops/index.php'),

    // Languages the *quote* (not the title/message above) is allowed to
    // come from. Fill in either or both: "id", "en". This is a separate,
    // wider pool than "locale" — e.g. an Indonesian-locale page can still
    // show an English quote for variety.
    // Leave empty (empty array) to allow every language.
    // Via .env: OOPS_QUOTE_LANGUAGES=id,en
    'quote_languages' => array_values(array_filter(explode(',', env('OOPS_QUOTE_LANGUAGES', 'id,en')))),

    // Genres allowed to show up, e.g. "wise", "humor", "formal", or any
    // custom label you tag in the quote files.
    // Leave empty (empty array) to allow every genre.
    // Via .env: OOPS_QUOTE_GENRES=humor,wise
    'quote_genres' => array_values(array_filter(explode(',', env('OOPS_QUOTE_GENRES', '')))),

    // Title, message & insight per status code, per locale. "insight" is a
    // short, fixed line of its own — calm and reflective rather than
    // apologetic — shown right before the quote. Unlike the quote (picked
    // at random from a pool), it's guaranteed to actually fit the
    // situation, so it doesn't depend on the random pick landing well.
    // Add or change freely — and add more locales if you need them beyond
    // "id"/"en".
    'messages' => [
        'id' => [
            403 => [
                'title' => 'Akses Ditolak',
                'message' => 'Sepertinya kamu tidak punya izin untuk membuka halaman ini.',
                'insight' => 'Tidak semua pintu diperuntukkan bagi semua orang.',
            ],
            404 => [
                'title' => 'Halaman Tidak Ditemukan',
                'message' => 'Halaman yang kamu cari sepertinya sudah pindah, atau memang tidak pernah ada.',
                'insight' => 'Tidak semua jalan menuju ke tempat yang kita duga, tapi setiap perjalanan mengajarkan sesuatu.',
            ],
            419 => [
                'title' => 'Sesi Kedaluwarsa',
                'message' => 'Halaman ini sudah terlalu lama dibuka. Coba muat ulang, ya.',
                'insight' => 'Jeda sebentar bisa menyegarkan perjalanan. Silakan coba lagi.',
            ],
            429 => [
                'title' => 'Terlalu Banyak Percobaan',
                'message' => 'Pelan-pelan saja. Tarik napas sebentar, lalu coba lagi.',
                'insight' => 'Sistem sehebat apa pun tetap butuh waktu untuk bernapas.',
            ],
            500 => [
                'title' => 'Ada yang Salah di Server',
                'message' => 'Bukan salahmu, kok. Tim kami sedang membereskannya.',
                'insight' => 'Setiap sistem hebat pernah mengalami kegagalan. Yang penting adalah bagaimana ia bangkit kembali.',
            ],
            503 => [
                'title' => 'Sedang Pemeliharaan',
                'message' => 'Kami sedang melakukan sedikit perbaikan. Sebentar lagi kembali normal.',
                'insight' => 'Sistem yang baik juga butuh waktu untuk berkembang dan menjadi lebih baik.',
            ],
        ],
        'en' => [
            403 => [
                'title' => 'Access Denied',
                'message' => 'Looks like you don\'t have permission to open this page.',
                'insight' => 'Not every door is meant to be opened by everyone.',
            ],
            404 => [
                'title' => 'Page Not Found',
                'message' => 'The page you\'re looking for may have moved, or never existed.',
                'insight' => 'Not every path leads where we expect, but every journey teaches something.',
            ],
            419 => [
                'title' => 'Session Expired',
                'message' => 'This page has been open for a while. Try refreshing it.',
                'insight' => 'A little pause can reset the journey. Please try again.',
            ],
            429 => [
                'title' => 'Too Many Attempts',
                'message' => 'Slow down for a moment, then try again.',
                'insight' => 'Even great systems need a moment to breathe.',
            ],
            500 => [
                'title' => 'Something Went Wrong on Our End',
                'message' => 'Not your fault — our team is already on it.',
                'insight' => 'Every great system has moments of failure. What matters is how it recovers.',
            ],
            503 => [
                'title' => 'Under Maintenance',
                'message' => 'We\'re making a few improvements. Back to normal shortly.',
                'insight' => 'Great systems also need time to improve and grow.',
            ],
        ],
    ],

    // Used for any status code not listed in "messages.{locale}" above.
    'default_message' => [
        'id' => [
            'title' => 'Terjadi Kesalahan',
            'message' => 'Ada sesuatu yang tidak berjalan semestinya.',
            'insight' => 'Setiap sistem punya masanya masing-masing — ini pun akan berlalu.',
        ],
        'en' => [
            'title' => 'Something Went Wrong',
            'message' => 'Something didn\'t go as expected.',
            'insight' => 'Every system has its moments — this one will pass too.',
        ],
    ],

    // Small UI labels shown on the page, per locale.
    'ui' => [
        'id' => [
            'back_home' => 'Kembali ke Beranda',
            'unknown_author' => 'Anonim',
        ],
        'en' => [
            'back_home' => 'Back to Home',
            'unknown_author' => 'Unknown',
        ],
    ],

    // Visual theme: light/dark mode and an optional logo/brand image.
    'theme' => [
        // "system" (follow the visitor's OS/browser setting — default),
        // "light" to always use the light palette, or "dark" to always
        // use the dark one, regardless of the visitor's own preference.
        // Via .env: OOPS_THEME_MODE=dark
        'mode' => env('OOPS_THEME_MODE', 'system'),

        // Override any color token per scheme. Leave a value null to
        // keep the package's built-in default for that token. "accent"
        // overrides the per-status accent color (the compass, padlock,
        // etc. all use the same accent when this is set) — leave it
        // null to keep each status's own hue.
        'colors' => [
            'light' => [
                'bg' => env('OOPS_COLOR_LIGHT_BG'),
                'card' => env('OOPS_COLOR_LIGHT_CARD'),
                'text' => env('OOPS_COLOR_LIGHT_TEXT'),
                'muted' => env('OOPS_COLOR_LIGHT_MUTED'),
                'border' => env('OOPS_COLOR_LIGHT_BORDER'),
                'accent' => env('OOPS_COLOR_LIGHT_ACCENT'),
            ],
            'dark' => [
                'bg' => env('OOPS_COLOR_DARK_BG'),
                'card' => env('OOPS_COLOR_DARK_CARD'),
                'text' => env('OOPS_COLOR_DARK_TEXT'),
                'muted' => env('OOPS_COLOR_DARK_MUTED'),
                'border' => env('OOPS_COLOR_DARK_BORDER'),
                'accent' => env('OOPS_COLOR_DARK_ACCENT'),
            ],
        ],

        // An image shown in place of the built-in status icon, e.g. your
        // app's logo. Leave "url" null to keep the icon. Accepts anything
        // a browser can load: asset(), Storage::url(), a full https://
        // URL, etc.
        'logo' => [
            'url' => env('OOPS_LOGO_URL'),
            'width' => env('OOPS_LOGO_WIDTH', 56),
            'height' => env('OOPS_LOGO_HEIGHT', 56),
        ],

        // Horizontal placement of the icon/logo badge: "left" (default)
        // or "center". Everything below it (title, message, quote,
        // button) stays left-aligned either way.
        // Via .env: OOPS_ICON_ALIGN=center
        'icon_align' => env('OOPS_ICON_ALIGN', 'left'),
    ],

];
