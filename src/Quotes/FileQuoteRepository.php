<?php

namespace Naiskit\LaravelOops\Quotes;

class FileQuoteRepository implements QuoteRepository
{
    public function __construct(
        protected ?string $path,
        protected string $fallbackPath
    ) {}

    public function all(): array
    {
        $file = ($this->path && is_file($this->path)) ? $this->path : $this->fallbackPath;

        $quotes = require $file;

        return is_array($quotes) ? $quotes : [];
    }

    public function forStatus(int $status, array $languages = [], array $genres = []): array
    {
        $all = $this->all();

        $pool = $all[$status] ?? [];

        if (empty($pool)) {
            $pool = $all['general'] ?? [];
        }

        if (empty($pool)) {
            return [];
        }

        // Relax the filters step by step so a narrow language/genre config
        // never leaves the error page without a quote at all.
        foreach ([[$languages, $genres], [$languages, []], [[], []]] as [$lang, $genre]) {
            $filtered = $this->filter($pool, $lang, $genre);

            if (! empty($filtered)) {
                return $filtered;
            }
        }

        return $pool;
    }

    public function random(int $status, array $languages = [], array $genres = []): array
    {
        $quotes = $this->forStatus($status, $languages, $genres);

        if (empty($quotes)) {
            return [];
        }

        return $quotes[array_rand($quotes)];
    }

    protected function filter(array $pool, array $languages, array $genres): array
    {
        return array_values(array_filter($pool, function (array $quote) use ($languages, $genres) {
            $languageMatches = empty($languages) || in_array($quote['lang'] ?? null, $languages, true);
            $genreMatches = empty($genres) || in_array($quote['genre'] ?? null, $genres, true);

            return $languageMatches && $genreMatches;
        }));
    }
}
