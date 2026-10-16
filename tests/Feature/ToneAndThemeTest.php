<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Naiskit\LaravelOops\Tests\TestCase;

class ToneAndThemeTest extends TestCase
{
    protected const TONES = ['formal', 'casual', 'fun', 'wise', 'brief', 'technical'];

    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);
    }

    public function test_formal_is_the_default_tone(): void
    {
        $response = $this->get('/throw/404');

        $response->assertSee('Mohon maaf, halaman yang Anda tuju tidak dapat kami temukan.', false);
    }

    public function test_the_casual_tone_uses_everyday_wording(): void
    {
        config(['oops.tone' => 'casual']);

        $response = $this->get('/throw/404');

        $response->assertSee('Halaman yang kamu cari sepertinya sudah pindah, atau memang tidak pernah ada.');
    }

    public function test_the_fun_tone_uses_playful_wording(): void
    {
        config(['oops.tone' => 'fun']);

        $response = $this->get('/throw/404');

        $response->assertSee('Waduh, kamu nyasar! Halaman ini lagi main petak umpet, dan sejauh ini dia yang menang.');
    }

    public function test_the_error_title_and_label_are_the_same_for_every_tone(): void
    {
        foreach (self::TONES as $tone) {
            config(['oops.tone' => $tone]);

            $response = $this->get('/throw/404');

            $response->assertSee('<title>404 | Halaman Tidak Ditemukan</title>', false);
            $response->assertSee('<h1>Halaman Tidak Ditemukan</h1>', false);
            $response->assertSee('Error 404');
        }
    }

    public function test_the_tone_only_changes_the_wording_below_the_title(): void
    {
        $strip = fn (string $html) => preg_replace('#<body>.*</body>#s', '', $html);
        $pages = [];

        foreach (self::TONES as $tone) {
            config(['oops.tone' => $tone]);
            $pages[$tone] = $strip($this->get('/throw/404')->getContent());
        }

        // Same <head> (title and styles included) for every tone.
        foreach (self::TONES as $tone) {
            $this->assertSame($pages['formal'], $pages[$tone], $tone);
        }
    }

    public function test_the_page_has_no_light_or_dark_mode(): void
    {
        $html = $this->get('/throw/404')->getContent();

        $this->assertStringNotContainsString('prefers-color-scheme', $html);
        $this->assertStringNotContainsString('color-scheme', $html);
    }

    public function test_each_tone_has_its_own_copy_for_every_language(): void
    {
        foreach (['id', 'en'] as $lang) {
            $bundled = array_map(fn ($file) => basename($file, '.php'), glob(__DIR__."/../../resources/lang/{$lang}/tones/*.php"));
            $this->assertEqualsCanonicalizing(self::TONES, $bundled, "every bundled tone is covered here ({$lang})");
        }

        foreach (self::TONES as $tone) {
            foreach (['id', 'en'] as $lang) {
                $copy = trans("oops::tones/{$tone}", [], $lang);

                $this->assertIsArray($copy, "{$tone}/{$lang}");
                $this->assertSame([403, 404, 419, 429, 500, 503], array_keys($copy['statuses']), "{$tone}/{$lang}");

                foreach ([...$copy['statuses'], $copy['default']] as $text) {
                    $this->assertArrayNotHasKey('title', $text, "{$tone}/{$lang}: titles live in errors.php");
                    $this->assertNotEmpty($text['message'], "{$tone}/{$lang}");
                    $this->assertNotEmpty($text['support'], "{$tone}/{$lang}");
                }

                foreach (['back_home', 'go_back'] as $label) {
                    $this->assertNotEmpty($copy[$label], "{$tone}/{$lang}/{$label}");
                }
            }
        }
    }

    public function test_every_language_has_a_title_for_every_status(): void
    {
        foreach (['id', 'en'] as $lang) {
            $errors = trans('oops::errors', [], $lang);

            $this->assertSame([403, 404, 419, 429, 500, 503], array_keys($errors['statuses']), $lang);
            $this->assertNotEmpty($errors['default'], $lang);
            $this->assertNotEmpty($errors['error_label'], $lang);
        }
    }

    public function test_the_wise_tone_opens_with_a_quote(): void
    {
        config(['oops.tone' => 'wise']);

        $response = $this->get('/throw/404');

        $response->assertSee('<blockquote>“Malu bertanya, sesat di jalan.”</blockquote>', false);
        $response->assertSee('<figcaption>Peribahasa</figcaption>', false);
        $response->assertSee('<h1>Halaman Tidak Ditemukan</h1>', false);
    }

    public function test_the_other_tones_have_no_quote(): void
    {
        foreach (['formal', 'casual', 'fun', 'brief', 'technical'] as $tone) {
            config(['oops.tone' => $tone]);

            $this->assertStringNotContainsString('<blockquote>', $this->get('/throw/404')->getContent(), $tone);
        }
    }

    public function test_an_app_can_add_its_own_tone_by_dropping_in_a_file(): void
    {
        $files = new Filesystem;
        $dir = $this->app->langPath('vendor/oops/id/tones');
        $files->ensureDirectoryExists($dir);
        // Partial on purpose: only the 404 message.
        $files->put($dir.'/kantor.php', "<?php return ['statuses' => [404 => ['message' => 'Pesan versi kantor.']]];");

        try {
            config(['oops.tone' => 'kantor']);

            $response = $this->get('/throw/404');

            $response->assertSee('Pesan versi kantor.');
            // Everything it doesn't define comes from the default tone.
            $response->assertSee('Silakan periksa kembali alamat yang Anda tuju, atau kembali ke beranda.');
            $response->assertSee('Kembali ke Beranda');
            $response->assertSee('<h1>Halaman Tidak Ditemukan</h1>', false);
        } finally {
            $files->deleteDirectory($this->app->langPath('vendor'));
        }
    }

    public function test_a_tone_name_that_is_not_a_plain_slug_falls_back_to_formal(): void
    {
        foreach (['../errors', 'tones/fun', 'fun.php', ''] as $tone) {
            config(['oops.tone' => $tone]);

            $response = $this->get('/throw/404');

            $response->assertStatus(404);
            $response->assertSee('Mohon maaf, halaman yang Anda tuju tidak dapat kami temukan.', false);
        }
    }

    public function test_an_unknown_tone_falls_back_to_formal(): void
    {
        config(['oops.tone' => 'sarcastic']);

        $response = $this->get('/throw/404');

        $response->assertSee('Mohon maaf, halaman yang Anda tuju tidak dapat kami temukan.', false);
    }

    public function test_the_page_offers_home_and_go_back_and_shows_where_it_came_from(): void
    {
        $response = $this->get('/throw/404?token=secret');

        $response->assertSee('class="btn btn-primary"', false);
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee('onclick="history.back()"', false);
        $response->assertSee('Halaman Sebelumnya');
        $response->assertSee('<span>http://localhost/throw/404</span>', false);
        // The query string may carry tokens, so it is never shown.
        $response->assertDontSee('token=secret');
    }

    public function test_the_footer_says_powered_by_naiskit_by_default(): void
    {
        $response = $this->get('/throw/404');

        $response->assertSee('Powered by <a href="https://github.com/naiskit"', false);
        $response->assertSee('rel="noopener">Naiskit</a>', false);
        $response->assertSee('<svg class="naiskit-logo"', false);
    }

    public function test_the_powered_by_footer_can_be_turned_off(): void
    {
        config(['oops.powered_by' => false]);

        $response = $this->get('/throw/404');

        $response->assertDontSee('Powered by');
        $response->assertDontSee('<svg class="naiskit-logo"', false);
    }

    public function test_each_status_gets_its_own_illustration_and_color(): void
    {
        $expected = [
            403 => '#4f46e5', 404 => '#2563eb', 419 => '#0284c7',
            429 => '#d97706', 500 => '#e11d48', 503 => '#0d9488',
        ];

        foreach ($expected as $status => $accent) {
            $html = $this->get("/throw/{$status}")->getContent();

            $this->assertStringContainsString("data-status=\"{$status}\"", $html);
            $this->assertStringContainsString("--accent: {$accent};", $html);
        }
    }

    public function test_the_reference_code_is_highlighted_in_the_support_line(): void
    {
        $html = $this->get('/throw/500')->getContent();

        $this->assertMatchesRegularExpression('#<code>OOPS-500-[0-9A-F]{4}</code>#', $html);
    }

    public function test_default_is_the_default_theme(): void
    {
        $html = $this->get('/throw/404')->getContent();

        $this->assertStringContainsString('class="illustration"', $html);
        $this->assertStringNotContainsString('class="mascot"', $html);
    }

    public function test_the_astronaut_theme_shows_the_mascot_card(): void
    {
        config(['oops.theme' => 'astronaut']);

        $response = $this->get('/throw/404');

        $response->assertSee('<img class="mascot" src="data:image/png;base64,', false);
        $response->assertSee('<div class="code-big">404</div>', false);
        $response->assertSee('<h1>Halaman Tidak Ditemukan</h1>', false);
        $response->assertSee('Mohon maaf, halaman yang Anda tuju tidak dapat kami temukan.', false);
        $response->assertSee('onclick="history.back()"', false);
        $response->assertSee('http://localhost/throw/404');
        $response->assertSee('Powered by <a href="https://github.com/naiskit"', false);
        $response->assertDontSee('class="illustration"', false);
    }

    public function test_the_astronaut_theme_shows_the_tone_quote_and_reference(): void
    {
        config(['oops.theme' => 'astronaut', 'oops.tone' => 'wise']);

        $html = $this->get('/throw/500')->getContent();

        $this->assertStringContainsString('<p>“Sepandai-pandai tupai melompat, akhirnya jatuh juga.”</p>', $html);
        $this->assertMatchesRegularExpression('#<code>OOPS-500-[0-9A-F]{4}</code>#', $html);
    }

    public function test_an_unknown_theme_falls_back_to_default(): void
    {
        foreach (['does-not-exist', '../themes/astronaut', 'themes.astronaut', ''] as $theme) {
            config(['oops.theme' => $theme]);

            $response = $this->get('/throw/404');

            $response->assertStatus(404);
            $response->assertSee('class="illustration"', false);
        }
    }

    public function test_an_app_can_add_its_own_theme_by_dropping_in_a_view(): void
    {
        $files = new Filesystem;
        $dir = $this->app->resourcePath('views/vendor/oops/themes');
        $files->ensureDirectoryExists($dir);
        $files->put($dir.'/kantor.blade.php', 'Theme kantor: {{ $code }} {{ $title }}');

        try {
            config(['oops.theme' => 'kantor']);

            $this->get('/throw/404')->assertSee('Theme kantor: 404 Halaman Tidak Ditemukan');
        } finally {
            $files->deleteDirectory($this->app->resourcePath('views/vendor/oops'));
        }
    }

    public function test_every_theme_renders_every_status(): void
    {
        foreach (['default', 'astronaut'] as $theme) {
            foreach ([403, 404, 419, 429, 500, 503] as $status) {
                config(['oops.theme' => $theme]);

                $response = $this->get("/throw/{$status}");

                $response->assertStatus($status);
                $response->assertSee("{$status} | ", false);
            }
        }
    }
}
