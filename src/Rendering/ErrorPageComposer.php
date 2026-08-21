<?php

namespace Naiskit\LaravelOops\Rendering;

use Illuminate\Contracts\Foundation\Application;
use Naiskit\LaravelOops\Quotes\QuoteRepository;

/**
 * Builds the view name + data for a status code — shared by the exception
 * renderer (LaravelOopsServiceProvider) and the `oops:preview` command, so
 * both always render exactly the same page for a given status.
 */
class ErrorPageComposer
{
    /**
     * Default accent color per status (light/dark), keyed the same way the
     * views are: a status code, or "general" for the fallback view. Each
     * status Blade file used to hardcode its own pair of these; centralizing
     * them here is what lets `oops.theme.colors.*.accent` override all of
     * them at once.
     */
    protected const STATUS_ACCENTS = [
        404 => ['light' => '#6d28d9', 'dark' => '#a78bfa'],
        403 => ['light' => '#be123c', 'dark' => '#fb7185'],
        419 => ['light' => '#b45309', 'dark' => '#fbbf24'],
        429 => ['light' => '#0f766e', 'dark' => '#2dd4bf'],
        500 => ['light' => '#dc2626', 'dark' => '#f87171'],
        503 => ['light' => '#475569', 'dark' => '#94a3b8'],
        'general' => ['light' => '#52525b', 'dark' => '#a1a1aa'],
    ];

    /**
     * Base palette tokens, before any `oops.theme.colors` override.
     */
    protected const BASE_COLORS = [
        'light' => [
            'bg' => '#f5f5f4',
            'card' => '#ffffff',
            'text' => '#1c1917',
            'muted' => '#78716c',
            'border' => '#e7e5e4',
        ],
        'dark' => [
            'bg' => '#18181b',
            'card' => '#232326',
            'text' => '#f4f4f5',
            'muted' => '#a1a1aa',
            'border' => '#313134',
        ],
    ];

    public function __construct(
        protected Application $app,
        protected QuoteRepository $quotes
    ) {}

    /**
     * @return array{view: string, data: array<string, mixed>}
     */
    public function compose(int $status): array
    {
        $lang = $this->resolveLang();
        $copy = $this->resolveCopy($lang, $status);
        $view = $this->resolveView($status);
        $reference = $this->generateReference($status);

        return [
            'view' => $view,
            'data' => [
                'code' => $status,
                'title' => $copy['title'],
                'message' => $copy['message'],
                'insight' => $copy['insight'] ?? null,
                'support' => $this->resolveSupport($copy, $reference),
                'reference' => $reference,
                'showFooter' => (bool) config('oops.show_footer', true),
                'lang' => $lang,
                'backHomeLabel' => config("oops.ui.{$lang}.back_home") ?? config('oops.ui.id.back_home'),
                'unknownAuthorLabel' => config("oops.ui.{$lang}.unknown_author") ?? config('oops.ui.id.unknown_author'),
                'quote' => $this->resolveQuote($status, $lang),
                ...$this->resolveTheme($view),
            ],
        ];
    }

    public function resolveView(int $status): string
    {
        if ($view = config('oops.view')) {
            return $view;
        }

        $view = "oops::{$status}";

        return $this->app->make('view')->exists($view) ? $view : 'oops::general';
    }

    /**
     * The single language driving everything on the page — copy and quotes
     * alike (see `resolveQuote()`). Falls back to the app's own locale
     * (`config('app.locale')`), then to "id" if that locale has no
     * translation here.
     */
    public function resolveLang(): string
    {
        $lang = config('oops.lang') ?: $this->app->getLocale();

        return array_key_exists($lang, config('oops.messages', [])) ? $lang : 'id';
    }

    public function resolveCopy(string $lang, int $status): array
    {
        return config("oops.messages.{$lang}.{$status}")
            ?? config("oops.messages.id.{$status}")
            ?? config("oops.default_message.{$lang}")
            ?? config('oops.default_message.id');
    }

    /**
     * A short, human-shareable code identifying this particular render
     * (e.g. "OOPS-500-A82F") — shown to the visitor via the "{ref}" token
     * in "support" copy, and logged by LaravelOopsServiceProvider
     * alongside the real exception, so a reported code can be traced back
     * to the matching log entry.
     */
    public function generateReference(int $status): string
    {
        return sprintf('OOPS-%d-%s', $status, strtoupper(bin2hex(random_bytes(2))));
    }

    /**
     * The "support" line for this status/locale, with "{ref}" replaced by
     * the render's reference code. Returns null when the resolved copy
     * has no "support" entry — the view simply skips the line then.
     */
    protected function resolveSupport(array $copy, string $reference): ?string
    {
        if (empty($copy['support'])) {
            return null;
        }

        return str_replace('{ref}', $reference, $copy['support']);
    }

    /**
     * A random quote for the status, strictly in $lang — an "id" page never
     * shows an "en" quote and vice versa. `QuoteRepository::forStatus()`
     * relaxes its own filters (genre, then language) so it never comes back
     * empty, which is the right default for the repository as a general
     * tool, but a wrong-language quote is worse than no quote at all here —
     * so anything the repository's own relaxation let through in the wrong
     * language gets discarded rather than shown; the view already skips the
     * quote block cleanly when there isn't one.
     *
     * Also avoids a back-to-back repeat of the last quote shown to this
     * visitor (tracked in session, keyed per status), so refreshing an
     * error page doesn't show the same text twice in a row. Falls back to
     * a plain random pick when no session is available (e.g. an unmatched
     * route never reaches session middleware, or an Artisan console
     * context) or when the pool has only one quote.
     *
     * @return array{text: string, author: string, source: ?string, lang: string, genre: string, meaning: ?string}|array{}
     */
    protected function resolveQuote(int $status, string $lang): array
    {
        $pool = $this->quotes->forStatus($status, [$lang], config('oops.quote_genres', []));
        $pool = array_values(array_filter($pool, fn (array $q) => $q['lang'] === $lang));

        if (empty($pool)) {
            return [];
        }

        $request = $this->app->bound('request') ? $this->app->make('request') : null;
        $session = $request && $request->hasSession() ? $request->session() : null;
        $key = "oops.last_quote.{$status}";
        $last = $session?->get($key);

        $candidates = count($pool) > 1
            ? array_values(array_filter($pool, fn (array $q) => $q['text'] !== $last))
            : $pool;

        if (empty($candidates)) {
            $candidates = $pool;
        }

        $quote = $candidates[array_rand($candidates)];

        $session?->put($key, $quote['text']);

        return $quote;
    }

    /**
     * @return array{themeMode: string, colorsLight: array<string, string>, colorsDark: array<string, string>, logoUrl: ?string, logoWidth: int|string, logoHeight: int|string, iconAlign: string, bgImage: ?string, sideBg: string, sideText: string, mascotImage: ?string, bgOverlay: string}
     */
    protected function resolveTheme(string $view): array
    {
        // Keyed off the resolved *view*, not the raw status — forcing
        // oops.view to a different view (e.g. "oops::general") should
        // carry that view's whole identity, accent included.
        $key = explode('::', $view)[1] ?? $view;
        $key = is_numeric($key) ? (int) $key : $key;
        $defaultAccent = self::STATUS_ACCENTS[$key] ?? self::STATUS_ACCENTS['general'];

        $mode = in_array(config('oops.theme.mode'), ['light', 'dark'], true)
            ? config('oops.theme.mode')
            : 'system';

        $colorsLight = $this->resolveScheme('light', $defaultAccent['light']);

        return [
            'themeMode' => $mode,
            'colorsLight' => $colorsLight,
            'colorsDark' => $this->resolveScheme('dark', $defaultAccent['dark']),
            'logoUrl' => config('oops.theme.logo.url'),
            'logoWidth' => config('oops.theme.logo.width', 76),
            'logoHeight' => config('oops.theme.logo.height', 76),
            'iconAlign' => config('oops.theme.icon_align') === 'left' ? 'left' : 'center',
            'bgImage' => $this->resolveBackgroundImage(),
            // Sidebar fill/text: a soft, pale tint of the status's own
            // (light-mode) accent as the background, with the full-strength
            // accent as the text/icon color on top — reads as a gentle
            // color-coded panel rather than a bold solid block, and always
            // derived from the light accent so contrast holds in dark mode
            // too (the dark-mode accent is a pastel meant for text on a
            // dark page, not reliable as a base for this kind of mixing).
            'sideBg' => $this->mixWithWhite($colorsLight['accent'], 0.16),
            'sideText' => $colorsLight['accent'],
            'mascotImage' => $this->resolveMascot($key),
            // A faint wash of the status's own accent, layered over the
            // decorative background image so the color-coding carries onto
            // the page background too, not just the sidebar.
            'bgOverlay' => $this->hexToRgba($colorsLight['accent'], 0.04),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function resolveScheme(string $scheme, string $defaultAccent): array
    {
        $overrides = config("oops.theme.colors.{$scheme}", []);

        return array_merge(
            self::BASE_COLORS[$scheme],
            ['accent' => $defaultAccent],
            array_filter($overrides ?? [])
        );
    }

    /**
     * Blends a "#rrggbb" color toward white — $ratio is how much of the
     * original color survives (0.16 means 16% color, 84% white), producing
     * a pale, soft tint of it rather than a literal lighten/opacity trick.
     */
    protected function mixWithWhite(string $hex, float $ratio): string
    {
        $hex = ltrim($hex, '#');
        $mix = fn (int $channel): int => (int) round($channel * $ratio + 255 * (1 - $ratio));

        return sprintf(
            '#%02x%02x%02x',
            $mix((int) hexdec(substr($hex, 0, 2))),
            $mix((int) hexdec(substr($hex, 2, 2))),
            $mix((int) hexdec(substr($hex, 4, 2)))
        );
    }

    /**
     * Converts a "#rrggbb" color to an "rgba(r, g, b, alpha)" string, for
     * layering as a translucent color wash (e.g. over the background image)
     * rather than a fully opaque fill.
     */
    protected function hexToRgba(string $hex, float $alpha): string
    {
        $hex = ltrim($hex, '#');

        return sprintf(
            'rgba(%d, %d, %d, %s)',
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
            $alpha
        );
    }

    /**
     * A soft decorative background, inlined as a data URI so it renders
     * with zero extra requests and no publish step — consistent with the
     * rest of the page (inline CSS, inline SVG icons). Only used in light
     * mode; view is responsible for not applying it in dark mode. Turn off
     * entirely via `oops.theme.background_image` / `OOPS_BACKGROUND_IMAGE=false`.
     */
    protected function resolveBackgroundImage(): ?string
    {
        if (! config('oops.theme.background_image', true)) {
            return null;
        }

        $path = __DIR__.'/../../resources/assets/bg-light.jpg';

        if (! is_file($path)) {
            return null;
        }

        return 'data:image/jpeg;base64,'.base64_encode(file_get_contents($path));
    }

    /**
     * A small mascot illustration matching the status's mood (e.g. a
     * shrugging astronaut for 404, arms crossed for 403) — takes over the
     * sidebar's icon slot when a status has one and no custom logo is
     * configured (a logo always wins, same as it does over the built-in
     * SVG icon). The plain icon still appears separately, next to the
     * title in the body column. Only 403/404/419/429/500/503 have art;
     * "general" falls back to the plain icon in the sidebar too. Inlined
     * as a data URI, same reasoning as the background image. Turn off via
     * `oops.theme.mascot` / `OOPS_THEME_MASCOT=false`.
     */
    protected function resolveMascot(int|string $key): ?string
    {
        if (! config('oops.theme.mascot', true)) {
            return null;
        }

        $path = __DIR__."/../../resources/assets/mascot-{$key}.png";

        if (! is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode(file_get_contents($path));
    }
}
