<?php

namespace Naiskit\LaravelOops\Quotes;

interface QuoteRepository
{
    /**
     * All quotes, grouped by HTTP status code.
     *
     * @return array<int|string, array<int, array{text: string, author: string, source: ?string, lang: string, genre: string, meaning: ?string}>>
     */
    public function all(): array;

    /**
     * The quote pool for a single status code, filtered by language & genre.
     * Falls back to the "general" group when the status has no group of its
     * own, and relaxes the genre filter then the language filter when the
     * result is empty, so it never comes back with no quotes at all.
     *
     * @param  array<int, string>  $languages  empty = all languages
     * @param  array<int, string>  $genres  empty = all genres
     * @return array<int, array{text: string, author: string, source: ?string, lang: string, genre: string, meaning: ?string}>
     */
    public function forStatus(int $status, array $languages = [], array $genres = []): array;

    /**
     * One random quote matching the error type, language, and genre.
     *
     * @param  array<int, string>  $languages
     * @param  array<int, string>  $genres
     * @return array{text: string, author: string, source: ?string, lang: string, genre: string, meaning: ?string}|array{}
     */
    public function random(int $status, array $languages = [], array $genres = []): array;
}
