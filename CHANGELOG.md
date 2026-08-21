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

- `config('oops.theme.icon_align')` (`center` default, or `left`) to
  left-align the sidebar contents — the icon/logo badge and the large
  status code both follow the same alignment; the body column (title,
  insight, quote, support, button) is unaffected either way.
- `insight`: a short, fixed, locale-aware line per status
  (`config('oops.messages.{locale}.{status}.insight')`), shown after the
  title and before the quote. Falls back to `default_message.{locale}.insight`
  for an unlisted status, same as `title`/`message`.
- `support`: an optional, locale-aware line per status
  (`config('oops.messages.{locale}.{status}.support')`), shown below the
  quote to tell the visitor what to do next (contact an administrator,
  wait it out, try again). Include the literal token `{ref}` anywhere in
  it to have it replaced with a short reference code unique to that
  render (e.g. `OOPS-500-A82F`); leave it out for statuses that don't need
  one. Falls back to `default_message.{locale}.support` for an unlisted
  status.
- Every rendered error page now generates a reference code and logs it
  alongside the real exception (message, class, and stack trace) — `5xx`
  logs at `error`, everything else at `warning` — so a code a visitor
  reports back can be traced to the actual failure. The same code is what
  `{ref}` in `support` copy resolves to.
- A "Powered by Naiskit" footer linking back to this repo, shown by
  default under the button. `config('oops.show_footer')` (or
  `OOPS_SHOW_FOOTER=false`) turns it off for a white-labeled page.
- The random quote no longer repeats itself back-to-back: the last quote
  shown to a visitor is tracked in session (per status) and excluded from
  the next pick, so refreshing an error page doesn't show the same text
  twice in a row. Falls back to a plain random pick when no session is
  available (an unmatched route never reaches session middleware, or an
  Artisan console context) or when the pool has only one quote.

### Changed

- Redesigned the card as a two-column grid: a left sidebar holds the
  icon/logo badge and the status code shown as a large number, separating
  "this is an error page" from the title/insight/quote/support/button in
  the body column next to it. Replaces the old single-column layout where
  the code was a small line of text above the title. Collapses to a single
  stacked column below 560px wide.
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
- `message` is no longer rendered by the built-in views — shown right next
  to `insight`, the two nearly always read as saying the same thing twice.
  `message` still exists in config and is passed to the view (for anyone
  overriding it), it's just not part of the default layout anymore.
- The default icon/logo badge grew from 48px/24px icon to 76px/38px icon
  to better fill the sidebar column; `oops.theme.logo.width`/`height`
  default from 56 to 76 to match. Only affects installs that never
  configured a logo size of their own.
- The sidebar is now a solid block filled with the status's own accent
  color (white icon/code/label on top) instead of matching the page
  background — reads as an error page at a glance rather than a neutral
  info card. Widened from 190px to 240px and the status-code number grew
  from 40px to 64px to carry the bolder treatment. The fill always uses
  the *light*-mode accent value, even in dark mode, since that color is
  saturated enough to hold white text — the dark-mode accent is a lighter
  tint meant for text/borders on a dark page, not a solid fill.
- Rewrote every bundled quote's `meaning` line: previously written as a
  third-person explanation of the quote ("Suggests that...", "Menjelaskan
  bahwa...") — now a short reflection extending the quote's own thought,
  not annotating it from the outside. Wording-only; the `meaning` field's
  shape and behavior are unchanged.
- Re-curated the entire bundled quote library (all 7 status folders) down
  to 51 entries, each checked against a primary source (book, essay,
  speech, or poem) with the author and source cited. Several previously
  bundled "famous" quotes turned out to be confirmed misattributions and
  were dropped rather than repeated: "Success is not final, failure is not
  fatal..." (not Churchill — the International Churchill Society lists it
  as a false attribution), "The secret of getting ahead is getting
  started" (not Twain — origin untraceable), "Our greatest glory is not in
  never falling..." (not Confucius — traced to Oliver Goldsmith),
  "It does not matter how slowly you go..." (not Confucius — no verified
  source), "You are braver than you believe..." (not A.A. Milne — written
  by Carter Crocker for a 1997 Disney film), "By failing to prepare, you
  are preparing to fail" (not Benjamin Franklin — earliest known use is
  1919), and "Every wall is a door" (not a verified Emerson line — the
  closest sourced original is "every wall is a gate," now used instead).
  New additions include verified lines from Marcus Aurelius, Seneca,
  Tolstoy, Viktor Frankl, Nelson Mandela, Rumi, Herman Melville, Chairil
  Anwar, Pramoedya Ananta Toer, Sapardi Djoko Damono, and Ahmad Fuadi,
  among others. Indonesian-language coverage stays uneven across
  statuses (403/404/419/429/general each have one or more verified `id`
  quotes; 500/503 currently have none) — well-sourced Indonesian quotes
  fitting "server failure" or "under maintenance" specifically proved hard
  to find without resorting to unattributed internet quote collections,
  which the sourcing standard above rules out.

### Removed

- `oops.ui.{locale}.quote_lead` config key — superseded by `insight` above.
- Every `'author' => 'Anonymous'` quote, plus the "Peribahasa Indonesia" /
  "Pepatah Programmer" / "Pepatah Internet" placeholder-author entries —
  none of these trace to a real, citable source. Some status/genre/language
  combinations (mainly `genre: humor` outside 403/404/419) now return an
  empty pool rather than a quote — verified, well-sourced humor proved to
  be the hardest kind of quote to find. The view already handles an empty
  pool gracefully (no quote block renders); nothing else changes.

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
