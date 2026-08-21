<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\LaravelOopsServiceProvider;
use Naiskit\LaravelOops\Tests\Support\AppOwnExceptionServiceProvider;
use Naiskit\LaravelOops\Tests\TestCase;

class CustomRenderableTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        // Registered — and therefore booted — before the package's own
        // provider, matching how an app's own renderable() callback
        // (e.g. from bootstrap/app.php's withExceptions()) runs before
        // package providers boot in a real Laravel application.
        return [
            AppOwnExceptionServiceProvider::class,
            LaravelOopsServiceProvider::class,
        ];
    }

    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    public function test_it_does_not_override_an_exception_the_app_already_handled(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertStatus(404);
        $response->assertSee('Custom app 404 response');
        $response->assertDontSee('Kembali ke Beranda');
    }

    public function test_it_still_handles_statuses_the_app_does_not_customize(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/500');

        $response->assertStatus(500);
        $response->assertSee('Kembali ke Beranda');
    }
}
