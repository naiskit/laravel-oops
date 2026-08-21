<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\Tests\TestCase;
use RuntimeException;

class SecurityTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw-secret', function () {
            throw new RuntimeException('DB password is hunter2, connection string: mysql://root:hunter2@127.0.0.1/prod');
        });

        $router->get('/throw-secret-http', function () {
            abort(500, 'Internal state dump: session_token=abc123secret');
        });
    }

    public function test_it_does_not_leak_the_exception_message(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw-secret');

        $response->assertStatus(500);
        $response->assertDontSee('hunter2', false);
        $response->assertDontSee('mysql://root', false);
    }

    public function test_it_does_not_leak_an_http_exceptions_message(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw-secret-http');

        $response->assertStatus(500);
        $response->assertDontSee('session_token', false);
        $response->assertDontSee('abc123secret', false);
    }

    public function test_it_does_not_leak_a_stack_trace_or_file_paths(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw-secret');
        $body = $response->getContent();

        $this->assertStringNotContainsString(__FILE__, $body);
        $this->assertStringNotContainsString('Stack trace', $body);
        $this->assertStringNotContainsString('#0 ', $body);
        $this->assertStringNotContainsString('vendor/laravel/framework', $body);
    }

    public function test_it_still_does_not_leak_when_forced_on_with_debug_true(): void
    {
        // Even in the "force it on for local testing" configuration, the
        // page must never surface exception internals — only APP_DEBUG's
        // own native page (which we deliberately step aside for) is meant
        // to show that level of detail.
        config(['app.debug' => true, 'oops.force' => true]);

        $response = $this->get('/throw-secret');
        $body = $response->getContent();

        $this->assertStringNotContainsString('hunter2', $body);
        $this->assertStringNotContainsString(__FILE__, $body);
        $this->assertStringNotContainsString('Stack trace', $body);
    }

    public function test_the_response_only_contains_configured_copy_and_a_quote(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw-secret');

        // Positive check to complement the negative ones above: the body
        // is exactly the configured 500 copy, nothing exception-derived.
        $response->assertSee('Ada yang Salah di Server');
        $response->assertSee('Bukan salahmu, kok. Tim kami sedang membereskannya.');
    }
}
