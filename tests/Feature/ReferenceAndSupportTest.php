<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Illuminate\Support\Facades\Log;
use Naiskit\LaravelOops\Tests\TestCase;

class ReferenceAndSupportTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    public function test_a_500_shows_a_support_line_with_a_reference_code(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/500');

        $response->assertSee('kode referensi berikut', false);
        $this->assertMatchesRegularExpression('/OOPS-500-[0-9A-F]{4}/', $response->getContent());
    }

    public function test_a_503_shows_support_copy_without_a_reference_code(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/503');

        $response->assertSee('Perkiraan layanan kembali tersedia akan diinformasikan oleh administrator.');
        $this->assertDoesNotMatchRegularExpression('/OOPS-503-[0-9A-F]{4}/', $response->getContent());
    }

    public function test_a_5xx_error_is_logged_at_error_level_with_the_reference_code(): void
    {
        config(['app.debug' => false]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function (string $message, array $context) {
                return str_starts_with($message, '[OOPS-500-')
                    && preg_match('/^OOPS-500-[0-9A-F]{4}$/', $context['oops_reference'] ?? '') === 1
                    && $context['status'] === 500
                    && array_key_exists('exception', $context);
            });

        $this->get('/throw/500');
    }

    public function test_a_4xx_error_is_logged_at_warning_level(): void
    {
        config(['app.debug' => false]);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(fn (string $message, array $context) => $context['status'] === 404);

        $this->get('/throw/404');
    }

    public function test_the_logged_reference_matches_the_one_shown_on_the_page(): void
    {
        config(['app.debug' => false]);

        $logged = null;

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function (string $message, array $context) use (&$logged) {
                $logged = $context['oops_reference'];

                return true;
            });

        $response = $this->get('/throw/500');

        $response->assertSee($logged);
    }

    public function test_the_footer_is_shown_by_default(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertSee('Powered by Laravel Oops');
        $response->assertSee('href="https://github.com/naiskit/laravel-oops"', false);
    }

    public function test_the_footer_can_be_turned_off(): void
    {
        config(['app.debug' => false, 'oops.show_footer' => false]);

        $response = $this->get('/throw/404');

        $response->assertDontSee('Powered by Laravel Oops');
    }
}
