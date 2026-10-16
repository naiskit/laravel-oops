<?php

namespace Naiskit\LaravelOops\Rendering;

use Illuminate\Contracts\Foundation\Application;

/**
 * Builds the view data for a status code, shared by the exception renderer
 * (LaravelOopsServiceProvider) and the `oops:preview` command, so both
 * always render exactly the same page for a given status.
 */
class ErrorPageComposer
{
    /**
     * A theme is the page design: one view at
     * resources/views/themes/{theme}.blade.php. Any view there is a theme:
     * an app adds its own by dropping one into
     * resources/views/vendor/oops/themes/. An unknown theme gets this one.
     */
    public const DEFAULT_THEME = 'default';

    /**
     * A tone is the wording below the error title (message, support line,
     * button labels, optional quote): one lang file per language at
     * resources/lang/{lang}/tones/{tone}.php. Any file there is a tone:
     * an app adds its own by dropping one into
     * lang/vendor/oops/{lang}/tones/. The error title comes from
     * resources/lang/{lang}/errors.php and is the same for every tone.
     *
     * The default tone is also the base: a custom tone only has to define
     * what it changes, everything else comes from this one.
     */
    public const DEFAULT_TONE = 'formal';

    /**
     * Supported languages. The first one is the default.
     */
    public const LANGS = ['en', 'id'];

    /**
     * Color per status: the hue tells the visitor whose side the problem
     * is on: blues for client-side issues they can act on (404 lost,
     * 403 restricted, 419 expired), amber for "slow down" (429), and warmer
     * or calmer hues for our side (500 rose: something broke, 503 teal:
     * planned maintenance). "accent" draws the illustration, "soft" is its
     * backdrop, "strong" is the readable text shade (WCAG AA on "soft").
     */
    protected const STATUS_COLORS = [
        403 => ['accent' => '#4f46e5', 'soft' => '#e0e7ff', 'strong' => '#4338ca'],
        404 => ['accent' => '#2563eb', 'soft' => '#dbeafe', 'strong' => '#1d4ed8'],
        419 => ['accent' => '#0284c7', 'soft' => '#e0f2fe', 'strong' => '#0369a1'],
        429 => ['accent' => '#d97706', 'soft' => '#fef3c7', 'strong' => '#b45309'],
        500 => ['accent' => '#e11d48', 'soft' => '#ffe4e6', 'strong' => '#be123c'],
        503 => ['accent' => '#0d9488', 'soft' => '#ccfbf1', 'strong' => '#0f766e'],
        'general' => ['accent' => '#475569', 'soft' => '#e2e8f0', 'strong' => '#334155'],
    ];

    public function __construct(protected Application $app) {}

    /**
     * @return array{view: string, data: array<string, mixed>}
     */
    public function compose(int $status): array
    {
        $lang = $this->pick(config('oops.lang'), self::LANGS);
        $translator = $this->app->make('translator');
        $errors = $translator->get('oops::errors', [], $lang, false);
        $copy = $this->toneCopy((string) config('oops.tone'), $lang);
        $text = $copy['statuses'][$status] ?? $copy['default'];
        $reference = $this->generateReference($status);

        return [
            'view' => $this->themeView((string) config('oops.theme')),
            'data' => [
                'code' => $status,
                'lang' => $lang,
                'title' => $errors['statuses'][$status] ?? $errors['default'],
                'quote' => $text['quote'] ?? null,
                'message' => $text['message'] ?? null,
                // "{ref}" in the support line becomes this render's reference code.
                'support' => isset($text['support']) ? str_replace('{ref}', $reference, $text['support']) : null,
                'reference' => $reference,
                // Where the error happened, so a screenshot tells which app
                // and page it came from. No query string: it may carry
                // tokens or signatures.
                'url' => $this->app->make('request')->url(),
                'labels' => [...array_filter($copy, 'is_string'), 'error_label' => $errors['error_label']],
                'colors' => self::STATUS_COLORS[$status] ?? self::STATUS_COLORS['general'],
                // Bundled theme images (resources/assets/...), inlined as
                // data URIs so the page needs no published or public files.
                'asset' => fn (string $path): ?string => $this->inlineAsset($path),
            ],
        ];
    }

    /**
     * A short, human-shareable code identifying this particular render
     * (e.g. "OOPS-500-A82F"), shown to the visitor via the "{ref}" token
     * in "support" copy, and logged by LaravelOopsServiceProvider
     * alongside the real exception, so a reported code can be traced back
     * to the matching log entry.
     */
    public function generateReference(int $status): string
    {
        return sprintf('OOPS-%d-%s', $status, strtoupper(bin2hex(random_bytes(2))));
    }

    /**
     * The configured value when it's one of $allowed, otherwise the first
     * (default) one, so a typo in .env degrades to the default page
     * instead of a 500.
     *
     * @param  array<int, string>  $allowed
     */
    protected function pick(mixed $value, array $allowed): string
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, $allowed, true) ? $value : $allowed[0];
    }

    /**
     * The copy for a tone, laid over the default tone so a custom tone
     * can be partial. An unknown tone (no file for this language) or a
     * name that isn't a plain slug gets the default tone, so a typo in .env
     * degrades gracefully instead of a 500.
     *
     * @return array<string, mixed>
     */
    protected function toneCopy(string $tone, string $lang): array
    {
        $translator = $this->app->make('translator');
        $base = $translator->get('oops::tones/'.self::DEFAULT_TONE, [], $lang, false);
        $tone = strtolower(trim($tone));

        // Plain slugs only: the name ends up in a file path.
        if ($tone === self::DEFAULT_TONE || ! preg_match('/^[a-z0-9_-]+$/', $tone)) {
            return $base;
        }

        // Translator returns the key itself when there's no such file.
        $copy = $translator->get("oops::tones/{$tone}", [], $lang, false);

        return is_array($copy) ? array_replace_recursive($base, $copy) : $base;
    }

    /**
     * The view for a theme. An unknown theme, or a name that isn't a plain
     * slug, gets the default theme, so a typo in .env never breaks the page.
     */
    protected function themeView(string $theme): string
    {
        $theme = strtolower(trim($theme));

        // Plain slugs only: the name ends up in a view path.
        if (preg_match('/^[a-z0-9_-]+$/', $theme) && $this->app->make('view')->exists("oops::themes.{$theme}")) {
            return "oops::themes.{$theme}";
        }

        return 'oops::themes.'.self::DEFAULT_THEME;
    }

    /**
     * A bundled image as a data URI, or null when it doesn't exist.
     * Only reads inside resources/assets.
     */
    public function inlineAsset(string $path): ?string
    {
        $root = realpath(__DIR__.'/../../resources/assets');
        $file = realpath($root.'/'.$path);

        if ($root === false || $file === false || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR) || ! is_file($file)) {
            return null;
        }

        $mime = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            default => null,
        };

        return $mime ? "data:{$mime};base64,".base64_encode((string) file_get_contents($file)) : null;
    }
}
