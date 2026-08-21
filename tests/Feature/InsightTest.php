<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\Tests\TestCase;

class InsightTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    public function test_it_shows_the_insight_line_for_a_known_status(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertSee(
            'Tidak semua jalan menuju ke tempat yang kita duga, tapi setiap perjalanan mengajarkan sesuatu.'
        );
    }

    public function test_the_insight_line_follows_the_resolved_locale(): void
    {
        config(['app.debug' => false, 'oops.locale' => 'en']);

        $response = $this->get('/throw/404');

        $response->assertSee(
            'Not every path leads where we expect, but every journey teaches something.'
        );
    }

    public function test_an_unlisted_status_falls_back_to_the_default_insight(): void
    {
        config(['app.debug' => false, 'oops.statuses' => [404, 418]]);

        $response = $this->get('/throw/418');

        $response->assertSee('Setiap sistem punya masanya masing-masing — ini pun akan berlalu.');
    }

    public function test_the_insight_line_appears_before_the_quote(): void
    {
        config(['app.debug' => false]);

        $body = $this->get('/throw/404')->getContent();

        $insightPos = strpos($body, 'class="insight"');
        $quotePos = strpos($body, '<blockquote>');

        $this->assertNotFalse($insightPos);
        $this->assertNotFalse($quotePos);
        $this->assertLessThan($quotePos, $insightPos);
    }

    public function test_no_insight_paragraph_is_rendered_when_the_status_copy_has_none(): void
    {
        config([
            'app.debug' => false,
            'oops.messages.id.404' => [
                'title' => 'Halaman Tidak Ditemukan',
                'message' => 'Halaman yang kamu cari sepertinya sudah pindah.',
            ],
        ]);

        $response = $this->get('/throw/404');

        $response->assertDontSee('class="insight"', false);
    }
}
