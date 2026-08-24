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

    // Language for everything on the page — title, message, insight,
    // support, the "back home" button (see "messages"/"ui" below), and
    // the quote pool itself (resources/quotes/): an "id" page only draws
    // from "id" quotes, an "en" page only from "en" ones. Leave null to
    // follow the app's own locale (config('app.locale')), falling back to
    // "id" if that locale has no translation here.
    // Via .env: OOPS_LANG=en
    'lang' => env('OOPS_LANG'),

    // Path to the app's own quote index (result of publishing tag
    // "oops-quotes", which creates a resources/quotes/oops/ folder with
    // index.php + one subfolder per status code). Falls back to the
    // package's bundled quotes when not published.
    'quotes_path' => resource_path('quotes/oops/index.php'),

    // Genres allowed to show up, e.g. "wise", "humor", "formal", or any
    // custom label you tag in the quote files.
    // Leave empty (empty array) to allow every genre.
    // Via .env: OOPS_QUOTE_GENRES=humor,wise
    'quote_genres' => array_values(array_filter(explode(',', env('OOPS_QUOTE_GENRES', '')))),

    // Title, message, insight & support per status code, per locale.
    // "insight" is a short, fixed line of its own — calm and reflective
    // rather than apologetic — shown right before the quote. Unlike the
    // quote (picked at random from a pool), it's guaranteed to actually
    // fit the situation, so it doesn't depend on the random pick landing
    // well. "support" is an optional line shown near the bottom, telling
    // the visitor what to do next (contact an administrator, wait it out,
    // etc.) — include the literal token "{ref}" anywhere in it to have it
    // replaced with this render's reference code (see "reference" below);
    // leave "{ref}" out for statuses that don't need one (e.g. 403, 503).
    // Add or change freely — and add more locales if you need them beyond
    // "id"/"en".
    'messages' => [
        'id' => [
            403 => [
                'title' => 'Akses Ditolak',
                'message' => 'Sepertinya kamu tidak punya izin untuk membuka halaman ini.',
                'insight' => 'Tidak semua pintu diperuntukkan bagi semua orang.',
                'support' => 'Jika kamu merasa seharusnya memiliki akses, silakan hubungi administrator.',
            ],
            404 => [
                'title' => 'Halaman Tidak Ditemukan',
                'message' => 'Halaman yang kamu cari sepertinya sudah pindah, atau memang tidak pernah ada.',
                'insight' => 'Tidak semua jalan menuju ke tempat yang kita duga, tapi setiap perjalanan mengajarkan sesuatu.',
                'support' => 'Jika kamu yakin halaman ini seharusnya tersedia, silakan hubungi administrator.',
            ],
            419 => [
                'title' => 'Sesi Kedaluwarsa',
                'message' => 'Halaman ini sudah terlalu lama dibuka. Coba muat ulang, ya.',
                'insight' => 'Jeda sebentar bisa menyegarkan perjalanan. Silakan coba lagi.',
                'support' => 'Coba muat ulang halaman. Jika masalah berlanjut, hubungi administrator.',
            ],
            429 => [
                'title' => 'Terlalu Banyak Percobaan',
                'message' => 'Pelan-pelan saja. Tarik napas sebentar, lalu coba lagi.',
                'insight' => 'Sistem sehebat apa pun tetap butuh waktu untuk bernapas.',
                'support' => 'Tunggu sebentar sebelum mencoba lagi. Jika masalah berlanjut, hubungi administrator.',
            ],
            500 => [
                'title' => 'Ada yang Salah di Server',
                'message' => 'Bukan salahmu, kok. Tim kami sedang membereskannya.',
                'insight' => 'Setiap sistem hebat pernah mengalami kegagalan. Yang penting adalah bagaimana ia bangkit kembali.',
                'support' => 'Jika masalah terus terjadi, silakan hubungi administrator dan sertakan kode referensi berikut: {ref}',
            ],
            503 => [
                'title' => 'Sedang Pemeliharaan',
                'message' => 'Kami sedang melakukan sedikit perbaikan. Sebentar lagi kembali normal.',
                'insight' => 'Sistem yang baik juga butuh waktu untuk berkembang dan menjadi lebih baik.',
                'support' => 'Perkiraan layanan kembali tersedia akan diinformasikan oleh administrator.',
            ],
        ],
        'en' => [
            403 => [
                'title' => 'Access Denied',
                'message' => 'Looks like you don\'t have permission to open this page.',
                'insight' => 'Not every door is meant to be opened by everyone.',
                'support' => 'If you believe you should have access, please contact the administrator.',
            ],
            404 => [
                'title' => 'Page Not Found',
                'message' => 'The page you\'re looking for may have moved, or never existed.',
                'insight' => 'Not every path leads where we expect, but every journey teaches something.',
                'support' => 'If you\'re sure this page should exist, please contact the administrator.',
            ],
            419 => [
                'title' => 'Session Expired',
                'message' => 'This page has been open for a while. Try refreshing it.',
                'insight' => 'A little pause can reset the journey. Please try again.',
                'support' => 'Try reloading the page. If the problem continues, contact the administrator.',
            ],
            429 => [
                'title' => 'Too Many Attempts',
                'message' => 'Slow down for a moment, then try again.',
                'insight' => 'Even great systems need a moment to breathe.',
                'support' => 'Please wait a moment before trying again. If the problem continues, contact the administrator.',
            ],
            500 => [
                'title' => 'Something Went Wrong on Our End',
                'message' => 'Not your fault — our team is already on it.',
                'insight' => 'Every great system has moments of failure. What matters is how it recovers.',
                'support' => 'If the problem keeps happening, please contact the administrator and include the following reference code: {ref}',
            ],
            503 => [
                'title' => 'Under Maintenance',
                'message' => 'We\'re making a few improvements. Back to normal shortly.',
                'insight' => 'Great systems also need time to improve and grow.',
                'support' => 'The administrator will announce when the service is expected to be back.',
            ],
        ],
    ],

    // Used for any status code not listed in "messages.{locale}" above.
    'default_message' => [
        'id' => [
            'title' => 'Terjadi Kesalahan',
            'message' => 'Ada sesuatu yang tidak berjalan semestinya.',
            'insight' => 'Setiap sistem punya masanya masing-masing — ini pun akan berlalu.',
            'support' => 'Jika masalah terus terjadi, silakan hubungi administrator dan sertakan kode referensi berikut: {ref}',
        ],
        'en' => [
            'title' => 'Something Went Wrong',
            'message' => 'Something didn\'t go as expected.',
            'insight' => 'Every system has its moments — this one will pass too.',
            'support' => 'If the problem keeps happening, please contact the administrator and include the following reference code: {ref}',
        ],
    ],

    // Small UI labels shown on the page, per locale.
    'ui' => [
        'id' => [
            'back_home' => 'Kembali ke Beranda',
            'unknown_author' => 'Anonim',
            'quote_lead' => 'Sedikit bekal untuk kamu bawa.',
        ],
        'en' => [
            'back_home' => 'Back to Home',
            'unknown_author' => 'Unknown',
            'quote_lead' => 'A little something to take with you.',
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
            'width' => env('OOPS_LOGO_WIDTH', 76),
            'height' => env('OOPS_LOGO_HEIGHT', 76),
        ],

        // Alignment of the sidebar contents (icon/logo + the large status
        // code) within their own column: "center" (default — reads best
        // for a badge-like number treatment) or "left". The body column
        // (title, insight, quote, button) is unaffected either way.
        // Via .env: OOPS_ICON_ALIGN=left
        'icon_align' => env('OOPS_ICON_ALIGN', 'center'),

        // A soft decorative background (resources/assets/bg-light.jpg),
        // inlined as a data URI, covering the page behind the card. Only
        // applies in light mode. Set to false to go back to a flat color.
        // Via .env: OOPS_BACKGROUND_IMAGE=false
        'background_image' => env('OOPS_BACKGROUND_IMAGE', true),

        // A small mascot illustration in the sidebar, matching each
        // status's mood (resources/assets/mascot-{status}.png) — takes
        // over the icon slot when a status has art and no custom logo is
        // configured. Set to false to fall back to the plain icon.
        // Via .env: OOPS_THEME_MASCOT=false
        'mascot' => env('OOPS_THEME_MASCOT', true),
    ],

    // A small "Powered by Naiskit" line in the footer, linking back
    // to the package repo. Turn off for a fully white-labeled page.
    // Via .env: OOPS_SHOW_FOOTER=false
    'show_footer' => env('OOPS_SHOW_FOOTER', true),

];
