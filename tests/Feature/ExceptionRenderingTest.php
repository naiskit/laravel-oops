<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Routing\Router;
use Illuminate\Validation\ValidationException;
use Naiskit\LaravelOops\Tests\TestCase;
use RuntimeException;

class ExceptionRenderingTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        /** @var Router $router */
        $router->get('/throw/{status}', function (int $status) {
            abort($status);
        });

        $router->get('/throw-generic', function () {
            throw new RuntimeException('boom');
        });

        $router->get('/login', fn () => 'login page')->name('login');

        $router->get('/throw-auth', function () {
            throw new AuthenticationException;
        });

        $router->post('/throw-validation', function () {
            throw ValidationException::withMessages(['field' => 'The field is required.']);
        });
    }

    public function test_it_renders_a_friendly_page_for_a_known_status(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw/404');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
    }

    public function test_it_uses_the_dedicated_view_per_status(): void
    {
        config(['app.debug' => false]);

        $response403 = $this->get('/throw/403');
        $response500 = $this->get('/throw/500');

        // Each status view uses its own accent color, so the rendered
        // <style> block differs even though they share the same layout.
        $response403->assertSee('#be123c', false);
        $response500->assertSee('#dc2626', false);
    }

    public function test_it_renders_a_friendly_page_for_a_generic_uncaught_exception(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw-generic');

        $response->assertStatus(500);
        $response->assertSee('Ada yang Salah di Server');
    }

    public function test_it_falls_back_to_the_general_view_for_an_unlisted_status(): void
    {
        config(['app.debug' => false, 'oops.statuses' => [404, 418]]);

        $response = $this->get('/throw/418');

        $response->assertStatus(418);
        $response->assertSee('Terjadi Kesalahan');
    }

    public function test_it_does_not_intercept_json_requests(): void
    {
        config(['app.debug' => false]);

        $response = $this->getJson('/throw/404');

        $response->assertStatus(404);
        $response->assertHeader('content-type', 'application/json');
        $response->assertDontSee('Kembali ke Beranda');
    }

    public function test_it_intercepts_a_plain_html_request_but_not_a_json_one_for_the_same_route(): void
    {
        config(['app.debug' => false]);

        $html = $this->get('/throw/404');
        $json = $this->withHeaders(['Accept' => 'application/json'])->get('/throw/404');

        $html->assertSee('Kembali ke Beranda');
        $json->assertHeader('content-type', 'application/json');
        $json->assertDontSee('Kembali ke Beranda');
    }

    public function test_it_leaves_validation_exceptions_alone(): void
    {
        config(['app.debug' => false]);

        $response = $this->postJson('/throw-validation', []);

        // Native Laravel validation error shape, proving our callback
        // returned null instead of rendering a friendly page over it.
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('field');
    }

    public function test_it_is_disabled_when_app_debug_is_true(): void
    {
        config(['app.debug' => true, 'oops.force' => false]);

        $response = $this->get('/throw/404');

        $response->assertStatus(404);
        $response->assertDontSee('Kembali ke Beranda');
    }

    public function test_it_can_be_forced_even_when_app_debug_is_true(): void
    {
        config(['app.debug' => true, 'oops.force' => true]);

        $response = $this->get('/throw/404');

        $response->assertStatus(404);
        $response->assertSee('Kembali ke Beranda');
    }

    public function test_it_leaves_authentication_exceptions_alone(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/throw-auth');

        // Proves our callback returned null and let Laravel's normal
        // auth handling redirect to the login route, instead of us
        // rendering a friendly page over it.
        $response->assertRedirect('/login');
    }

    public function test_oops_force_env_var_is_parsed_correctly(): void
    {
        // Unlike the config()-array tests above, this exercises the real
        // env('OOPS_FORCE', false) parsing path — .env values are always
        // strings, so this proves "true" is actually cast to a boolean.
        putenv('OOPS_FORCE=true');
        $this->refreshApplication();
        config(['app.debug' => true]);

        $response = $this->get('/throw/404');

        $response->assertStatus(404);
        $response->assertSee('Kembali ke Beranda');

        putenv('OOPS_FORCE');
    }

    public function test_it_can_be_disabled_entirely(): void
    {
        config(['app.debug' => false]);
        putenv('OOPS_ENABLED=false');
        $this->refreshApplication();

        $response = $this->get('/throw/404');

        $response->assertStatus(404);
        $response->assertDontSee('Kembali ke Beranda');

        putenv('OOPS_ENABLED');
    }
}
