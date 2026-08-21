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

    // Title & message per status code, per locale. Add or change freely —
    // and add more locales if you need them beyond "id"/"en".
    'messages' => [
        'id' => [
            403 => [
                'title' => 'Akses Ditolak',
                'message' => 'Sepertinya kamu tidak punya izin untuk membuka halaman ini.',
            ],
            404 => [
                'title' => 'Halaman Tidak Ditemukan',
                'message' => 'Halaman yang kamu cari sepertinya sudah pindah, atau memang tidak pernah ada.',
            ],
            419 => [
                'title' => 'Sesi Kedaluwarsa',
                'message' => 'Halaman ini sudah terlalu lama dibuka. Coba muat ulang, ya.',
            ],
            429 => [
                'title' => 'Terlalu Banyak Percobaan',
                'message' => 'Pelan-pelan saja. Tarik napas sebentar, lalu coba lagi.',
            ],
            500 => [
                'title' => 'Ada yang Salah di Server',
                'message' => 'Bukan salahmu, kok. Tim kami sedang membereskannya.',
            ],
            503 => [
                'title' => 'Sedang Pemeliharaan',
                'message' => 'Kami sedang melakukan sedikit perbaikan. Sebentar lagi kembali normal.',
            ],
        ],
        'en' => [
            403 => [
                'title' => 'Access Denied',
                'message' => 'Looks like you don\'t have permission to open this page.',
            ],
            404 => [
                'title' => 'Page Not Found',
                'message' => 'The page you\'re looking for may have moved, or never existed.',
            ],
            419 => [
                'title' => 'Session Expired',
                'message' => 'This page has been open for a while. Try refreshing it.',
            ],
            429 => [
                'title' => 'Too Many Attempts',
                'message' => 'Slow down for a moment, then try again.',
            ],
            500 => [
                'title' => 'Something Went Wrong on Our End',
                'message' => 'Not your fault — our team is already on it.',
            ],
            503 => [
                'title' => 'Under Maintenance',
                'message' => 'We\'re making a few improvements. Back to normal shortly.',
            ],
        ],
    ],

    // Used for any status code not listed in "messages.{locale}" above.
    'default_message' => [
        'id' => [
            'title' => 'Terjadi Kesalahan',
            'message' => 'Ada sesuatu yang tidak berjalan semestinya.',
        ],
        'en' => [
            'title' => 'Something Went Wrong',
            'message' => 'Something didn\'t go as expected.',
        ],
    ],

    // Small UI labels shown on the page, per locale. "quote_lead" is the
    // line shown right above the quote, so it reads as the page talking to
    // the visitor rather than a random citation dropped in.
    'ui' => [
        'id' => [
            'back_home' => 'Kembali ke Beranda',
            'unknown_author' => 'Anonim',
            'quote_lead' => 'Sambil menunggu, ini buat kamu:',
        ],
        'en' => [
            'back_home' => 'Back to Home',
            'unknown_author' => 'Unknown',
            'quote_lead' => 'While you wait, here\'s something for you:',
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
    ],

];
