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

        return [
            'view' => $view,
            'data' => [
                'code' => $status,
                'title' => $copy['title'],
                'message' => $copy['message'],
                'insight' => $copy['insight'] ?? null,
                'locale' => $locale,
                'backHomeLabel' => config("oops.ui.{$locale}.back_home") ?? config('oops.ui.id.back_home'),
                'unknownAuthorLabel' => config("oops.ui.{$locale}.unknown_author") ?? config('oops.ui.id.unknown_author'),
                'quote' => $this->quotes->random(
                    $status,
                    config('oops.quote_languages', []),
                    config('oops.quote_genres', [])
                ),
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
