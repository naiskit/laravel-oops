<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\Tests\TestCase;

class QuoteLanguageTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    public function test_an_id_page_never_shows_an_english_quote(): void
    {
        config(['app.debug' => false, 'oops.lang' => 'id']);

        $response = $this->get('/throw/404');

        $response->assertDontSee('Not all those who wander are lost', false);
        $response->assertDontSee('Two roads diverged', false);
    }

    public function test_an_en_page_never_shows_an_indonesian_quote(): void
    {
        config(['app.debug' => false, 'oops.lang' => 'en']);

        $response = $this->get('/throw/404');

        $response->assertDontSee('Kulari dari gedong', false);
    }

    public function test_a_status_with_no_quote_in_the_resolved_lang_shows_no_quote_block(): void
    {
        // 500's bundled pool currently has no verified "id" quote — the
        // page should simply skip the blockquote rather than falling back
        // to an English one.
        config(['app.debug' => false, 'oops.lang' => 'id']);

        $response = $this->get('/throw/500');

        $response->assertDontSee('<blockquote>', false);
    }
}
