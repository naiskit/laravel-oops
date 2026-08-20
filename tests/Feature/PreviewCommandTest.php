<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Naiskit\LaravelOops\Tests\TestCase;

class PreviewCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach ([404, 500] as $status) {
            $path = storage_path("app/oops-preview-{$status}.html");

            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_it_writes_a_previewable_html_file(): void
    {
        $this->artisan('oops:preview', ['status' => 404])
            ->assertSuccessful();

        $path = storage_path('app/oops-preview-404.html');

        $this->assertFileExists($path);
        $this->assertStringContainsString('Halaman Tidak Ditemukan', file_get_contents($path));
        $this->assertStringContainsString('Sambil menunggu, ini buat kamu:', file_get_contents($path));
    }

    public function test_it_defaults_to_status_404(): void
    {
        $this->artisan('oops:preview')->assertSuccessful();

        $this->assertFileExists(storage_path('app/oops-preview-404.html'));
    }

    public function test_it_can_preview_any_status(): void
    {
        $this->artisan('oops:preview', ['status' => 500])
            ->assertSuccessful();

        $path = storage_path('app/oops-preview-500.html');

        $this->assertFileExists($path);
        $this->assertStringContainsString('Ada yang Salah di Server', file_get_contents($path));
    }
}
