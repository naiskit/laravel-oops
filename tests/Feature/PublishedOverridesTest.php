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

    public function test_a_published_view_takes_precedence_over_the_bundled_one(): void
    {
        config(['app.debug' => false]);

        $files = new Filesystem;
        $publishedDir = $this->app->resourcePath('views/vendor/oops');
        $files->ensureDirectoryExists($publishedDir.'/themes');
        $files->put($publishedDir.'/themes/default.blade.php', 'Published override for {{ $code }}.');

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

    public function test_published_lang_copy_overrides_the_bundled_copy(): void
    {
        config(['app.debug' => false]);

        $files = new Filesystem;
        $publishedDir = $this->app->langPath('vendor/oops/id');
        $files->ensureDirectoryExists($publishedDir);
        $files->put($publishedDir.'/errors.php', "<?php return ['statuses' => [404 => 'Judul Kustom']];");

        try {
            $response = $this->get('/throw/404');

            $response->assertSee('Judul Kustom');
            // Keys not overridden still come from the bundled file.
            $response->assertSee('Kembali ke Beranda');
        } finally {
            $files->deleteDirectory($this->app->langPath('vendor'));
        }
    }
}
