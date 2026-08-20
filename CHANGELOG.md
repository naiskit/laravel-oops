# Changelog

All notable changes to `laravel-oops` are documented here. The format is
based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this
project follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Changed

- Renamed the `wise` quote genre from its former Indonesian-only label
  `bijak`, so genre labels read consistently in English — update
  `quote_genres` / `OOPS_QUOTE_GENRES` and any published quote files
  accordingly.

### Added

- Friendly error pages for 403, 404, 419, 429, 500, and 503, auto-registered
  via a `renderable()` exception handler callback — no wiring required after
  `composer require`.
- A dedicated Blade view per status code (own icon and accent color),
  sharing a common layout partial, resolved automatically and falling back
  to a general view for any other status code.
- A quote shown on each error page, matched to the kind of error, filterable
  by language (`id`/`en`) and genre (`wise`, `humor`, `formal`, or custom),
  with a short neutral `meaning` note per quote.
- Quotes and views both organized so new ones can be added without editing
  existing files — drop a file into the matching `resources/quotes/{status}/`
  folder, or add a `resources/views/{status}.blade.php`.
- `php artisan oops:quote {status}` to preview a quote from the CLI.
- Publishable config, quotes, and views (`oops-config`, `oops-quotes`,
  `oops-views` tags).
- Locale-aware title, message, and "Back to Home" label via
  `config('oops.locale')`, defaulting to the app's own `app.locale` and
  falling back to Indonesian for an unsupported locale — kept separate from
  the quote language pool, which stays a deliberate multi-language mix.
- A "quote lead" line above the quote (e.g. "While you wait, here's
  something for you:") so it reads as the page addressing the visitor
  instead of a disconnected citation; the quote block itself no longer uses
  a mismatched background box, so it sits as part of the card rather than
  looking like a cutout.
- `php artisan oops:preview {status=404}` renders a status's page to
  `storage/app/oops-preview-{status}.html` for a quick look in a browser,
  without needing to toggle `APP_DEBUG`/`oops.force` or trigger a real
  error. Shares its rendering logic with the exception handler via the new
  `Naiskit\LaravelOops\Rendering\ErrorPageComposer`.
