<?php

namespace Naiskit\LaravelOops\Tests\Unit;

use Naiskit\LaravelOops\Quotes\FileQuoteRepository;
use PHPUnit\Framework\TestCase;

class FileQuoteRepositoryTest extends TestCase
{
    protected function repository(): FileQuoteRepository
    {
        return new FileQuoteRepository(null, __DIR__.'/../../resources/quotes/index.php');
    }

    public function test_it_loads_quotes_for_every_known_status(): void
    {
        $all = $this->repository()->all();

        foreach ([404, 403, 419, 429, 500, 503, 'general'] as $key) {
            $this->assertArrayHasKey($key, $all);
            $this->assertNotEmpty($all[$key]);
        }
    }

    public function test_it_falls_back_to_general_for_an_unknown_status(): void
    {
        $repository = $this->repository();

        $this->assertSame($repository->all()['general'], $repository->forStatus(999));
    }

    public function test_it_filters_by_language(): void
    {
        $pool = $this->repository()->forStatus(404, ['id']);

        $this->assertNotEmpty($pool);

        foreach ($pool as $quote) {
            $this->assertSame('id', $quote['lang']);
        }
    }

    public function test_it_filters_by_genre(): void
    {
        $pool = $this->repository()->forStatus(404, [], ['humor']);

        $this->assertNotEmpty($pool);

        foreach ($pool as $quote) {
            $this->assertSame('humor', $quote['genre']);
        }
    }

    public function test_it_relaxes_filters_instead_of_returning_nothing(): void
    {
        $pool = $this->repository()->forStatus(404, ['fr'], ['sarcastic']);

        $this->assertNotEmpty($pool);
    }

    public function test_random_returns_one_quote_with_the_expected_shape(): void
    {
        $quote = $this->repository()->random(404);

        foreach (['text', 'author', 'source', 'lang', 'genre', 'meaning'] as $key) {
            $this->assertArrayHasKey($key, $quote);
        }
    }

    public function test_it_uses_a_custom_path_when_it_exists(): void
    {
        $repository = new FileQuoteRepository(
            __DIR__.'/../Fixtures/custom-quotes.php',
            __DIR__.'/../../resources/quotes/index.php'
        );

        $quote = $repository->random(404);

        $this->assertSame('Custom quote for testing.', $quote['text']);
    }

    public function test_it_ignores_a_missing_custom_path(): void
    {
        $repository = new FileQuoteRepository(
            __DIR__.'/../Fixtures/does-not-exist.php',
            __DIR__.'/../../resources/quotes/index.php'
        );

        $this->assertNotEmpty($repository->all());
    }
}
