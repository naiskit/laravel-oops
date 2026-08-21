<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Naiskit\LaravelOops\Tests\TestCase;

class PublishedOverridesTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    public function test_a_published_quotes_path_is_used_over_the_bundled_one(): void
    {
        config([
            'app.debug' => false,
            'oops.quotes_path' => __DIR__.'/../Fixtures/custom-quotes.php',
        ]);

        $response = $this->get('/throw/404');

        $response->assertSee('Custom quote for testing.');
    }

    public function test_a_published_view_takes_precedence_over_the_bundled_one(): void
    {
        config(['app.debug' => false]);

        $files = new Filesystem;
        $publishedDir = $this->app->resourcePath('views/vendor/oops');
        $files->ensureDirectoryExists($publishedDir);
        $files->put($publishedDir.'/404.blade.php', 'Published override for 404.');

        try {
            $response = $this->get('/throw/404');

            // Laravel's loadViewsFrom() automatically prefers a matching
            // file under resources/views/vendor/{namespace} over the
            // package's own — this proves that convention actually works
            // for this package's "oops" namespace, end to end.
            $response->assertSee('Published override for 404.');
        } finally {
            $files->deleteDirectory($publishedDir);
        }
    }

    public function test_oops_view_config_forces_a_single_view_for_every_status(): void
    {
        config(['app.debug' => false, 'oops.view' => 'oops::general']);

        $response = $this->get('/throw/404');

        // "general" uses a zinc accent (#52525b), not 404's own violet
        // (#6d28d9) — proves the forced view really is used instead of
        // the status-specific one.
        $response->assertSee('#52525b', false);
        $response->assertDontSee('#6d28d9', false);
    }
}
