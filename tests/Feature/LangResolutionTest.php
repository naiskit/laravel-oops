<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\Tests\TestCase;

class LangResolutionTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    public function test_english_is_the_default(): void
    {
        // TestCase pins "id"; read the package's own default instead.
        $default = (require __DIR__.'/../../config/oops.php')['lang'];
        config(['app.debug' => false, 'oops.lang' => $default]);

        $response = $this->get('/throw/404');

        $response->assertSee('Page Not Found');
        $response->assertSee('Back to Home');
        $response->assertSee('lang="en"', false);
    }

    public function test_indonesian_can_be_selected(): void
    {
        config(['app.debug' => false, 'oops.lang' => 'id']);

        $response = $this->get('/throw/404');

        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee('lang="id"', false);
    }

    public function test_it_ignores_the_apps_own_locale(): void
    {
        config(['app.debug' => false, 'oops.lang' => 'en']);
        $this->app->setLocale('id');

        $response = $this->get('/throw/404');

        $response->assertSee('Page Not Found');
    }

    public function test_an_unsupported_lang_falls_back_to_english(): void
    {
        config(['app.debug' => false, 'oops.lang' => 'fr']);

        $response = $this->get('/throw/404');

        $response->assertSee('Page Not Found');
        $response->assertSee('lang="en"', false);
    }

    public function test_lang_is_case_insensitive(): void
    {
        config(['app.debug' => false, 'oops.lang' => 'ID']);

        $response = $this->get('/throw/404');

        $response->assertSee('lang="id"', false);
    }
}
