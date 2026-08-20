<?php

namespace Naiskit\LaravelOops\Console;

use Illuminate\Console\Command;
use Naiskit\LaravelOops\Rendering\ErrorPageComposer;

class PreviewCommand extends Command
{
    protected $signature = 'oops:preview
        {status=404 : HTTP status code to preview, e.g. 404, 403, 419, 429, 500, 503}';

    protected $description = 'Render a friendly error page to a local HTML file so you can preview it in a browser';

    public function handle(ErrorPageComposer $composer): int
    {
        $status = (int) $this->argument('status');

        ['view' => $view, 'data' => $data] = $composer->compose($status);

        $html = view($view, $data)->render();

        $path = storage_path("app/oops-preview-{$status}.html");

        file_put_contents($path, $html);

        $this->info("Saved to: {$path}");
        $this->line('Open that file in a browser to preview the page.');

        return self::SUCCESS;
    }
}
