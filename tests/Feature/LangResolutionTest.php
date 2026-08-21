<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\Tests\TestCase;

class LangResolutionTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', function (int $status) {
            abort($status);
        });
    }

    public function test_it_follows_the_apps_locale_when_oops_lang_is_not_set(): void
    {
        config(['app.debug' => false, 'app.locale' => 'en', 'oops.lang' => null]);

        $response = $this->get('/throw/404');

        $response->assertSee('Page Not Found');
        $response->assertSee('Back to Home');
        $response->assertSee('lang="en"', false);
    }

    public function test_it_prefers_oops_lang_over_the_apps_locale(): void
    {
        config(['app.debug' => false, 'app.locale' => 'en', 'oops.lang' => 'id']);

        $response = $this->get('/throw/404');

        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee('lang="id"', false);
    }

    public function test_it_falls_back_to_indonesian_for_an_unsupported_lang(): void
    {
        config(['app.debug' => false, 'app.locale' => 'fr', 'oops.lang' => null]);

        $response = $this->get('/throw/404');

        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('lang="id"', false);
    }
}
