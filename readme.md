# laravel-oops

[![Latest Version on Packagist](https://img.shields.io/packagist/v/naiskit/laravel-oops.svg?style=flat-square)](https://packagist.org/packages/naiskit/laravel-oops)
[![Tests](https://img.shields.io/github/actions/workflow/status/naiskit/laravel-oops/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/naiskit/laravel-oops/actions/workflows/tests.yml)
[![codecov](https://img.shields.io/codecov/c/github/naiskit/laravel-oops?style=flat-square)](https://codecov.io/gh/naiskit/laravel-oops)
[![Total Downloads](https://img.shields.io/packagist/dt/naiskit/laravel-oops.svg?style=flat-square)](https://packagist.org/packages/naiskit/laravel-oops)
[![License](https://img.shields.io/packagist/l/naiskit/laravel-oops.svg?style=flat-square)](LICENSE.md)

Friendly, calm error pages for Laravel, in the tone that fits your app.

Two themes are bundled. Both show the error code, the error title, the
tone content, a "Home" and a "Back" button, and the page URL (without
query string) so you can tell which app an error came from.

- **`default`**: an open two-column layout with an illustration made for
  each status (magnifier for 404, shield for 403, hourglass for 419,
  speedometer for 429, server alert for 500, maintenance cone for 503).
  Each status has its own color that hints at whose side the problem is
  on: blues for issues the visitor can act on (403/404/419), amber for
  "slow down" (429), rose for "something broke on our side" (500), teal
  for planned maintenance (503). Soft blue-grey background with a faint
  dot pattern and waves tinted in the status color.
- **`astronaut`**: the original laravel-oops look. A card with a
  color-coded side panel holding an astronaut mascot for each status and
  the big status code.

Both stack on small screens. Run `composer serve` in this repo to see
them.

## Requirements

- PHP ^8.2
- Laravel ^11.0, ^12.0, or ^13.0

## Installation

```bash
composer require naiskit/laravel-oops
```

That's it. The service provider is auto-discovered. When `APP_DEBUG=false`,
unhandled 403 / 404 / 419 / 429 / 500 / 503 responses for HTML requests get
the friendly page.

## Configuration

Five settings, all via `.env`:

```dotenv
OOPS_ENABLED=true     # false = use Laravel's own error pages
OOPS_THEME=default    # default | astronaut | your own
OOPS_TONE=formal      # formal | casual | fun | wise | brief | technical | your own
OOPS_LANG=en          # en | id
OOPS_POWERED_BY=true  # false = hide "Powered by Naiskit" in the footer
```

| Setting | Default | Notes |
|---|---|---|
| `OOPS_ENABLED` | `true` | Turn off to fall back to Laravel's default error pages. |
| `OOPS_THEME` | `default` | The page design: `default`, `astronaut`, or [your own](#your-own-theme). An unknown value falls back to `default`. |
| `OOPS_TONE` | `formal` | The wording below the error title (the design stays the same). A bundled tone or [your own](#your-own-tone). An unknown value falls back to `formal`. |
| `OOPS_LANG` | `en` | `en` or `id`. An unknown value falls back to `en`. |
| `OOPS_POWERED_BY` | `true` | Shows "Powered by Naiskit" (linking to github.com/naiskit) at the bottom of the page. Set to `false` to hide it. |

### Tones

Every page reads top to bottom as: the **error code** ("Error 404"), the
**error title** ("Page Not Found"), then the **tone content**: the
message, the support line, and the button labels. The error code and
title are the same for every tone, so what went wrong is always stated
plainly; the tone only changes the content below them:

| Tone | Wording |
|---|---|
| `formal` | Polite and soft. "We're sorry, the page you are looking for could not be found." / "Mohon maaf, halaman yang Anda tuju tidak dapat kami temukan." |
| `casual` | Relaxed and everyday. "The page you're looking for may have moved, or never existed." / "Halaman yang kamu cari sepertinya sudah pindah…" |
| `fun` | Playful. "Oops, you're lost! This page is playing hide-and-seek…" / "Waduh, kamu nyasar! Halaman ini lagi main petak umpet…" |
| `wise` | Calm and reflective, opens with a short quote. "Not all those who wander are lost." (Tolkien) / "Malu bertanya, sesat di jalan." (peribahasa) |
| `brief` | Short and to the point, one sentence. "This page is not available." / "Halaman tidak tersedia." Suits internal tools and dashboards. |
| `technical` | What the status means and what to check, for admin panels used by IT or developers. "No route or resource matches this URL." / "Tidak ada route atau resource yang cocok dengan URL ini." Never shows exception details; for 500 it points to the reference code in the log. |

### Your own tone

Every file in `tones/` is a tone. To add one, drop a file into your app:

```php
// lang/vendor/oops/id/tones/kantor.php  →  OOPS_TONE=kantor
return [
    'statuses' => [
        404 => [
            'quote' => ['text' => '…', 'author' => '…'],   // optional
            'message' => 'Halaman ini tidak tersedia.',
            'support' => 'Hubungi tim IT di ext. 123.',
        ],
        // 403, 419, 429, 500, 503 …
    ],
    'default' => ['message' => '…', 'support' => '… {ref}'],
    'back_home' => '…',
    'go_back' => '…',
];
```

It only needs the keys it changes; everything else comes from `formal`.
Add one file per language you use (`en`, `id`). `{ref}` in `support`
becomes the reference code. Tone names are plain slugs (`a-z`, `0-9`,
`-`, `_`).

### Your own theme

A theme is one Blade view. To add one, drop it into your app at
`resources/views/vendor/oops/themes/{name}.blade.php` and set
`OOPS_THEME={name}`. Copying a bundled theme is the easiest start:

```bash
php artisan vendor:publish --tag=oops-views   # resources/views/vendor/oops/themes/
```

The view gets `$code`, `$title`, `$message`, `$support`, `$quote`
(`text`, `author`, or null), `$reference`, `$url`, `$lang`, `$labels`
(`error_label`, `back_home`, `go_back`), and `$colors` (`accent`, `soft`,
`strong`). Theme names are plain slugs (`a-z`, `0-9`, `-`, `_`).

There are no other settings: no light/dark mode, colors, or layout
options.

## How it works

The package registers a `renderable()` callback on Laravel's exception
handler. It only steps in when:

- the status is 403, 404, 419, 429, 500, or 503 (anything that isn't an
  `HttpException` counts as 500),
- the request does **not** expect JSON,
- `APP_DEBUG` is off,
- and nothing else (your own app code, another package) already handled
  the exception.

Auth redirects (`AuthenticationException`), validation errors
(`ValidationException`), and `HttpResponseException` are always left alone.

## Reference code

Pages for 5xx errors (and any status without its own copy) show a short
reference code, e.g. `OOPS-500-A82F`. Every render also logs a line with
the same code next to the real exception:

```
[OOPS-500-A82F] RuntimeException: Call to undefined method ...
```

so a code a visitor reports can be traced back to the matching log entry.
5xx logs at `error`; 403/404/419/429 log at `warning`.

## Previewing the page

```bash
php artisan oops:preview        # defaults to 404
php artisan oops:preview 500
```

Saves the exact page the exception handler would render to
`storage/app/oops-preview-{status}.html`. Works regardless of `APP_DEBUG`.

## Customizing (optional)

```bash
php artisan vendor:publish --tag=oops-config  # config/oops.php
php artisan vendor:publish --tag=oops-lang    # lang/vendor/oops/{en,id}/: errors.php (error titles), tones/{tone}.php (tone content)
php artisan vendor:publish --tag=oops-views   # resources/views/vendor/oops/themes/ (change a theme or add your own)
```

A published lang file only needs the keys you want to change. The rest
still comes from the package.

## Testing

```bash
composer install
composer test        # PHPUnit
composer format      # Laravel Pint
composer analyse     # PHPStan / Larastan
composer serve       # playground at http://127.0.0.1:8000, every theme × status × tone × language
```

`composer serve` runs the package in a throwaway Laravel app (Orchestra
Testbench) with `APP_DEBUG=false`, so each page goes through the real
exception handler. Defaults come from [`testbench.yaml`](testbench.yaml);
`?theme=`, `?tone=` and `?lang=` override them per request, e.g.
`/500?theme=astronaut&tone=casual&lang=id`.

`tests/Feature/SecurityTest.php` guards against exception details
(messages, stack traces, file paths) ever reaching the rendered page. See
[CONTRIBUTING.md](CONTRIBUTING.md) if you're touching anything near
exception rendering.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security Vulnerabilities

Please don't open a public issue for security vulnerabilities. See
[SECURITY.md](SECURITY.md) for how to report one.

## Credits

- [Naiskit](https://github.com/naiskit)
- [All Contributors](https://github.com/naiskit/laravel-oops/contributors)

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
