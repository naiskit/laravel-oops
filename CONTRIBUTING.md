# Contributing

Thanks for considering contributing to `laravel-oops`. Issues and pull
requests are welcome at
[naiskit/laravel-oops](https://github.com/naiskit/laravel-oops).

## Setting up

```bash
git clone git@github.com:naiskit/laravel-oops.git
cd laravel-oops
composer install
```

## Before opening a PR

Run all three checks that CI runs, and make sure they pass:

```bash
composer test      # PHPUnit with Orchestra Testbench, no real Laravel app needed
composer format     # Laravel Pint, auto-fixes style issues
composer analyse    # PHPStan / Larastan, level 5
composer serve      # playground at http://127.0.0.1:8000 to see every page in a browser
```

A few things specific to this package worth knowing before you dig in:

- **Themes and tones are separate.** A theme is the design: one view at
  `resources/views/themes/{theme}.blade.php` that renders every status and
  tone. Any view there is a theme. A tone is only wording: a lang file per
  language (`resources/lang/{en,id}/tones/{tone}.php`) with the tone
  content (message, support, button labels, optional quote). Any file
  there is a tone, with no list to register it in. Keep every bundled tone
  complete for both languages (the test suite checks). Error titles live in
  `resources/lang/{en,id}/errors.php` and are shared by every tone.
- **The exception renderer and `oops:preview` share one code path**:
  `Naiskit\LaravelOops\Rendering\ErrorPageComposer`. If you're changing how
  a page gets built (theme, tone, language), that's
  the place, not either caller.
- **Never let exception details reach the rendered page.** The whole point
  of this package is a safe fallback for `APP_DEBUG=false`, so the view must
  only ever see pre-configured copy, never `$exception->getMessage()`
  or similar. `tests/Feature/SecurityTest.php` exists specifically to catch
  a regression here; please keep it passing (and add to it if you touch
  anything near exception rendering).
- **CI tests against Laravel 11, 12, and 13** across PHP 8.2–8.4 (see
  `.github/workflows/tests.yml`). A change that only makes sense for one
  Laravel major is a sign it probably needs a version check or shouldn't be
  in this package.

## Reporting a security issue

Please don't open a public issue for security vulnerabilities. See
[SECURITY.md](SECURITY.md) instead.
