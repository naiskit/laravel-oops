<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\Tests\TestCase;

class ThemeTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/throw/{status}', fn (int $status) => abort($status));
    }

    public function test_system_mode_includes_both_light_and_dark_palettes_by_default(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertSee('color-scheme: light dark', false);
        $response->assertSee('@media (prefers-color-scheme: dark)', false);
        $response->assertSee('#6d28d9', false); // 404's light accent
        $response->assertSee('#a78bfa', false); // 404's dark accent
    }

    public function test_light_mode_forces_the_light_palette_only(): void
    {
        config(['app.debug' => false, 'oops.theme.mode' => 'light']);

        $response = $this->get('/throw/404');

        $response->assertSee('color-scheme: light;', false);
        $response->assertDontSee('@media (prefers-color-scheme: dark)', false);
        $response->assertSee('#6d28d9', false);
    }

    public function test_dark_mode_forces_the_dark_palette_only(): void
    {
        config(['app.debug' => false, 'oops.theme.mode' => 'dark']);

        $response = $this->get('/throw/404');

        $response->assertSee('color-scheme: dark;', false);
        $response->assertDontSee('@media (prefers-color-scheme: dark)', false);
        $response->assertSee('#a78bfa', false); // 404's dark accent, used directly
    }

    public function test_an_invalid_mode_falls_back_to_system(): void
    {
        config(['app.debug' => false, 'oops.theme.mode' => 'sepia']);

        $response = $this->get('/throw/404');

        $response->assertSee('color-scheme: light dark', false);
    }

    public function test_a_custom_accent_overrides_every_statuss_own_accent(): void
    {
        config([
            'app.debug' => false,
            'oops.theme.colors.light.accent' => '#123456',
            'oops.theme.colors.dark.accent' => '#654321',
        ]);

        $response = $this->get('/throw/404');

        $response->assertSee('#123456', false);
        $response->assertSee('#654321', false);
        $response->assertDontSee('#6d28d9', false);
        $response->assertDontSee('#a78bfa', false);
    }

    public function test_a_custom_base_color_overrides_the_default(): void
    {
        config(['app.debug' => false, 'oops.theme.colors.light.bg' => '#000000']);

        $response = $this->get('/throw/404');

        $response->assertSee('--bg:#000000;', false);
    }

    public function test_leaving_a_color_null_keeps_the_built_in_default(): void
    {
        config(['app.debug' => false, 'oops.theme.colors.light.bg' => null]);

        $response = $this->get('/throw/404');

        $response->assertSee('--bg:#f5f5f4;', false);
    }

    public function test_a_logo_replaces_the_built_in_icon(): void
    {
        config(['app.debug' => false, 'oops.theme.logo.url' => 'https://example.com/logo.png']);

        $response = $this->get('/throw/404');

        $response->assertSee('src="https://example.com/logo.png"', false);
        // 404's compass icon SVG path should no longer be rendered.
        $response->assertDontSee('polygon points="12,7 14,12 12,17 10,12"', false);
    }

    public function test_the_logo_respects_configured_dimensions(): void
    {
        config([
            'app.debug' => false,
            'oops.theme.logo.url' => 'https://example.com/logo.png',
            'oops.theme.logo.width' => 120,
            'oops.theme.logo.height' => 40,
        ]);

        $response = $this->get('/throw/404');

        $response->assertSee('width="120" height="40"', false);
    }

    public function test_without_a_logo_the_built_in_icon_is_used(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertSee('<svg viewBox="0 0 24 24">', false);
        $response->assertDontSee('<img src=', false);
    }

    public function test_the_icon_is_left_aligned_by_default(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertDontSee('margin-left:auto;margin-right:auto;', false);
    }

    public function test_icon_align_center_centers_the_built_in_icon(): void
    {
        config(['app.debug' => false, 'oops.theme.icon_align' => 'center']);

        $response = $this->get('/throw/404');

        $response->assertSee('class="icon-badge" style="margin-left:auto;margin-right:auto;"', false);
    }

    public function test_icon_align_center_centers_the_logo_too(): void
    {
        config([
            'app.debug' => false,
            'oops.theme.icon_align' => 'center',
            'oops.theme.logo.url' => 'https://example.com/logo.png',
        ]);

        $response = $this->get('/throw/404');

        $response->assertSee('src="https://example.com/logo.png"', false);
        $response->assertSee('class="logo-badge"', false);
        $response->assertSee('margin-left:auto;margin-right:auto;', false);
    }

    public function test_an_invalid_icon_align_falls_back_to_left(): void
    {
        config(['app.debug' => false, 'oops.theme.icon_align' => 'right']);

        $response = $this->get('/throw/404');

        $response->assertDontSee('margin-left:auto;margin-right:auto;', false);
    }

    public function test_the_error_code_is_left_aligned_alongside_the_icon_by_default(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertSee('<p class="code" style="">Error 404</p>', false);
    }

    public function test_icon_align_center_also_centers_the_error_code(): void
    {
        config(['app.debug' => false, 'oops.theme.icon_align' => 'center']);

        $response = $this->get('/throw/404');

        $response->assertSee('<p class="code" style="text-align:center;">Error 404</p>', false);
    }
}
