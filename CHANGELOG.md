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
- A soft decorative background image behind the card in light mode
  (`resources/assets/bg-light.jpg`, inlined as a data URI — no extra
  request, no publish step). `config('oops.theme.background_image')` (or
  `OOPS_BACKGROUND_IMAGE=false`) turns it off. Never shown in dark mode,
  forced or OS-triggered, regardless of the setting. It also carries a
  faint translucent wash of the status's own accent color layered on top
  (`ErrorPageComposer::hexToRgba()` + a flat-color `linear-gradient`), so
  the color-coding reads across the whole page background, not just the
  sidebar.
- A mascot illustration in the sidebar for 403/404/419/429/500/503 — a
  small astronaut character matching each status's mood (shrugging for
  "not found," arms crossed for "access denied," crying for "server
  error," etc.), inlined the same way as the background image. Takes over
  the sidebar's icon slot whenever no custom logo is configured (a logo
  always wins, same as it does over the plain icon). `oops::general` has
  no mascot of its own. `config('oops.theme.mascot')` (or
  `OOPS_THEME_MASCOT=false`) turns it off everywhere.
- The plain icon now also appears in a small badge next to the title in
  the body column, regardless of whether the sidebar is showing a logo,
  a mascot, or the icon itself — gives every status page a consistent
  visual anchor even when the sidebar art changes.

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
- The sidebar is now a soft, pale tint of the status's own accent color
  (icon/code/label in the full-strength accent on top) instead of matching
  the page background — reads as a color-coded panel at a glance rather
  than a neutral info card, without the intensity of a solid saturated
  fill. Widened from a fixed 190px column to a proportional 3fr/5fr grid
  split (~38% of the card) and the status-code number grew from 40px to
  64px to carry the bolder treatment. Both the tint and the text color
  always derive from the *light*-mode accent value, even in dark mode,
  since mixing the dark-mode pastel accent toward white would wash out
  inconsistently per status — the light accent gives a uniformly soft
  result everywhere (`ErrorPageComposer::mixWithWhite()`).
- Card grew from 680px to 760px max width to carry the bigger sidebar.
- Card's shadow is now a layered, softly purple-tinted `box-shadow`
  (matching the decorative background's own cool lavender tone) instead
  of a flat `0 1px 3px black` — reads as floating just above the page
  background rather than a flat cutout pasted on top of it.
- Rewrote every bundled quote's `meaning` line: previously written as a
  third-person explanation of the quote ("Suggests that...", "Menjelaskan
  bahwa...") — now a short reflection extending the quote's own thought,
  not annotating it from the outside. Wording-only; the `meaning` field's
  shape and behavior are unchanged.
- Re-curated the entire bundled quote library (all 7 status folders), each
  checked against a primary source (book, essay, speech, or poem) with the
  author and source cited. Several previously bundled "famous" quotes
  turned out to be confirmed misattributions and were dropped rather than
  repeated: "Success is not final, failure is not fatal..." (not Churchill
  — the International Churchill Society lists it as a false attribution),
  "The secret of getting ahead is getting started" (not Twain — origin
  untraceable), "Our greatest glory is not in never falling..." (not
  Confucius — traced to Oliver Goldsmith), "It does not matter how slowly
  you go..." (not Confucius — no verified source), "You are braver than
  you believe..." (not A.A. Milne — written by Carter Crocker for a 1997
  Disney film), "By failing to prepare, you are preparing to fail" (not
  Benjamin Franklin — earliest known use is 1919), and "Every wall is a
  door" (not a verified Emerson line — the closest sourced original is
  "every wall is a gate," now used instead). New additions include
  verified lines from Marcus Aurelius, Seneca, Tolstoy, Viktor Frankl,
  Nelson Mandela, Herman Melville, Chairil Anwar, Pramoedya Ananta Toer,
  Sapardi Djoko Damono, and Ahmad Fuadi, among others.
- A second, stricter verification pass on top of the one above — the bar
  moved from "is this attributed to the right person" to "is this the
  *exact* published text, from the *exact* named work, and not a
  paraphrase" (principle: no source, no quote). This caught several
  quotes that were attributed correctly but worded as a popularly-
  circulated paraphrase rather than the actual documented text, now
  corrected to the real wording: Marcus Aurelius's river-of-time line
  (Meditations IV.43, George Long's translation — the version bundled
  before didn't match Long's or any other identifiable named
  translation), Seneca's "short time to live" line (On the Shortness of
  Life, John W. Basore's translation — "short space of time... waste much
  of it," not the commonly-repeated "short time to live... waste a lot of
  it"), and Edison's "10,000 ways" line (the earliest documented version,
  per Edison: His Life and Inventions, 1910, is "I have gotten a lot of
  results! I know several thousand things that won't work" — the "I have
  not failed... 10,000 ways" phrasing is a later, looser popularization).
  Four quotes were dropped outright for failing the stricter bar: Rumi's
  "The wound is the place where the Light enters you" (Coleman Barks'
  interpretive verse translation, not a literal rendering of the Persian
  — explicitly a paraphrase by the translator's own description), Marie
  Curie's "Nothing in life is to be feared..." (even Wikiquote flags the
  primary source as unconfirmed, tracing only to a secondary biography
  describing it as something she "often said to reporters"), Emerson's
  "Adopt the pace of nature: her secret is patience" (repeatedly
  attributed across quote databases with no primary-source page ever
  found, and inconsistent essay citations between sources), Soekarno's
  "Gantungkan cita-citamu setinggi langit" (cited only via a secondary
  book reference that itself says the original speech occasion "sulit
  ditelusuri," i.e. untraceable), and Andrea Hirata's dream quote from
  *Sang Pemimpi* (multiple sources quote it with different, mutually
  inconsistent exact wording, so no version could be confirmed as the
  real novel text). The library now stands at 45 entries — smaller than
  the 51 from the previous pass, and below the 50–100 originally
  requested — because accuracy took priority over hitting a count.
  Indonesian-language coverage is thinner as a result: `general` now has
  only one verified `id` quote (Chairil Anwar), and 500/503 still have
  none.
- `oops.locale` and `oops.quote_languages` are unified into a single
  `oops.lang` (`OOPS_LANG` via `.env`) — one language now drives *both*
  the page's copy and its quote pool. Previously the two were
  independent by design (a quote could come from a wider language mix
  than the page's own text); now an `id` page only ever draws from `id`
  quotes and an `en` page only from `en` ones, with no cross-language
  fallback. If a status has no quote in the resolved language, the page
  simply renders without a quote block rather than showing one in the
  wrong language — 500 and 503 currently have no `id` quote of their own
  (see above), so an `id`-language visitor won't see a quote on those two
  specifically until real Indonesian ones are added.
  `ErrorPageComposer::resolveLocale()` is renamed `resolveLang()`.

### Removed

- `oops.ui.{lang}.quote_lead` config key — superseded by `insight` above.
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
