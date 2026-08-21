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
composer test      # PHPUnit — Orchestra Testbench, no real Laravel app needed
composer format     # Laravel Pint, auto-fixes style issues
composer analyse    # PHPStan / Larastan, level 5
```

A few things specific to this package worth knowing before you dig in:

- **Quotes and views are per-status.** Add a new quote by dropping a file
  into `resources/quotes/{status}/`, not by editing an existing one — see
  the "Quotes matched to the error" section in the readme. Same idea for
  views: each status has its own `resources/views/{status}.blade.php`,
  sharing only `layout.blade.php` for page chrome.
- **The exception renderer and `oops:preview` share one code path** —
  `Naiskit\LaravelOops\Rendering\ErrorPageComposer`. If you're changing how
  a page gets built (locale, copy, quote selection, view resolution), that's
  the place, not either caller.
- **Never let exception details reach the rendered page.** The whole point
  of this package is a safe fallback for `APP_DEBUG=false` — the view must
  only ever see pre-configured copy and quote data, never `$exception->getMessage()`
  or similar. `tests/Feature/SecurityTest.php` exists specifically to catch
  a regression here; please keep it passing (and add to it if you touch
  anything near exception rendering).
- **CI tests against Laravel 11, 12, and 13** across PHP 8.2–8.4 (see
  `.github/workflows/tests.yml`) — a change that only makes sense for one
  Laravel major is a sign it probably needs a version check or shouldn't be
  in this package.

## Reporting a security issue

Please don't open a public issue for security vulnerabilities — see
[SECURITY.md](SECURITY.md) instead.
