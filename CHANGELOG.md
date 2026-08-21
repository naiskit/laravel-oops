# Changelog

All notable changes to `laravel-oops` are documented here. The format is
based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this
project follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- Configurable theme via `config('oops.theme')`: force `light` or `dark`
  mode instead of following the visitor's own OS/browser preference,
  override any color token (including one `accent` for every status
  instead of each keeping its own hue), and show a logo image in place of
  the built-in icon.
- `SECURITY.md` and a security-focused test suite
  (`tests/Feature/SecurityTest.php`) asserting exception messages, stack
  traces, and file paths never reach the rendered page, including when
  `APP_DEBUG=true` + `oops.force=true`.
- CI now tests the full support matrix — PHP 8.2/8.3/8.4 × Laravel
  11/12/13 — plus PHPStan/Larastan (level 5) and code coverage reporting.
- `CONTRIBUTING.md` and Dependabot (composer + github-actions, weekly).
- Tests covering `ValidationException` handling, a custom app-level
  `renderable()` callback taking precedence over the package's own, the
  `OOPS_FORCE` env var through its real parsing path, and published
  views/quotes actually being picked up over the bundled ones.

- `config('oops.theme.icon_align')` (`left` default, or `center`) to
  center the icon/logo badge — everything else in the card (title,
  message, quote, button) stays left-aligned either way.
- `insight`: a short, fixed, locale-aware line per status
  (`config('oops.messages.{locale}.{status}.insight')`), shown after the
  message and before the quote. Falls back to `default_message.{locale}.insight`
  for an unlisted status, same as `title`/`message`.

### Changed

- Per-status accent colors moved out of the individual status Blade views
  and into `Naiskit\LaravelOops\Rendering\ErrorPageComposer`, which is what
  makes the new `oops.theme.colors.*.accent` override possible. No visual
  or behavioral change if you haven't touched the theme config — the
  default colors per status are unchanged.
- Replaced the "quote lead" line (a generic "here's something for you"
  framing that oversold the randomly-picked quote as personally curated)
  with `insight` — a line written specifically for each status, so it's
  guaranteed to fit even when the quote below it doesn't.
- The icon/logo badge now sizes itself to the logo's actual configured
  `width`/`height` instead of forcing a fixed 56×56 square — a non-square
  logo (e.g. a wide wordmark) used to get squished to fit both dimensions;
  it now renders at its intended size.

### Removed

- `oops.ui.{locale}.quote_lead` config key — superseded by `insight` above.

## [1.0.0] - 2026-08-20

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
  error. Shares its rendering logic with the exception handler via
  `Naiskit\LaravelOops\Rendering\ErrorPageComposer`.
