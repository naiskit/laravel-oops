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
        $locale = $this->resolveLocale();
        $copy = $this->resolveCopy($locale, $status);
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
                'locale' => $locale,
                'backHomeLabel' => config("oops.ui.{$locale}.back_home") ?? config('oops.ui.id.back_home'),
                'unknownAuthorLabel' => config("oops.ui.{$locale}.unknown_author") ?? config('oops.ui.id.unknown_author'),
                'quote' => $this->resolveQuote($status),
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

    public function resolveLocale(): string
    {
        $locale = config('oops.locale') ?: $this->app->getLocale();

        return array_key_exists($locale, config('oops.messages', [])) ? $locale : 'id';
    }

    public function resolveCopy(string $locale, int $status): array
    {
        return config("oops.messages.{$locale}.{$status}")
            ?? config("oops.messages.id.{$status}")
            ?? config("oops.default_message.{$locale}")
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
     * A random quote for the status, avoiding a back-to-back repeat of the
     * last one shown to this visitor (tracked in session, keyed per
     * status) — so refreshing an error page doesn't show the same text
     * twice in a row. Falls back to a plain random pick when no session is
     * available (e.g. an unmatched route never reaches session middleware,
     * or an Artisan console context) or when the pool has only one quote.
     *
     * @return array{text: string, author: string, source: ?string, lang: string, genre: string, meaning: ?string}|array{}
     */
    protected function resolveQuote(int $status): array
    {
        $pool = $this->quotes->forStatus(
            $status,
            config('oops.quote_languages', []),
            config('oops.quote_genres', [])
        );

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
     * @return array{themeMode: string, colorsLight: array<string, string>, colorsDark: array<string, string>, logoUrl: ?string, logoWidth: int|string, logoHeight: int|string, iconAlign: string}
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

        return [
            'themeMode' => $mode,
            'colorsLight' => $this->resolveScheme('light', $defaultAccent['light']),
            'colorsDark' => $this->resolveScheme('dark', $defaultAccent['dark']),
            'logoUrl' => config('oops.theme.logo.url'),
            'logoWidth' => config('oops.theme.logo.width', 56),
            'logoHeight' => config('oops.theme.logo.height', 56),
            'iconAlign' => config('oops.theme.icon_align') === 'center' ? 'center' : 'left',
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
}
