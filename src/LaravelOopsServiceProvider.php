<?php

namespace Naiskit\LaravelOops;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Naiskit\LaravelOops\Console\PreviewCommand;
use Naiskit\LaravelOops\Console\QuoteCommand;
use Naiskit\LaravelOops\Quotes\FileQuoteRepository;
use Naiskit\LaravelOops\Quotes\QuoteRepository;
use Naiskit\LaravelOops\Rendering\ErrorPageComposer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class LaravelOopsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oops.php', 'oops');

        $this->app->singleton(QuoteRepository::class, function ($app) {
            return new FileQuoteRepository(
                $app['config']->get('oops.quotes_path'),
                __DIR__.'/../resources/quotes/index.php'
            );
        });

        $this->app->singleton(ErrorPageComposer::class, function ($app) {
            return new ErrorPageComposer($app, $app->make(QuoteRepository::class));
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'oops');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/oops.php' => config_path('oops.php'),
            ], 'oops-config');

            $this->publishes([
                __DIR__.'/../resources/quotes' => resource_path('quotes/oops'),
            ], 'oops-quotes');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/oops'),
            ], 'oops-views');

            $this->commands([QuoteCommand::class, PreviewCommand::class]);
        }

        $this->registerExceptionRenderer();
    }

    protected function registerExceptionRenderer(): void
    {
        if (! config('oops.enabled')) {
            return;
        }

        $this->app->make(ExceptionHandler::class)->renderable(
            fn (Throwable $e, $request) => $this->renderOopsPage($e, $request)
        );
    }

    protected function renderOopsPage(Throwable $e, $request): ?Response
    {
        if ($this->shouldSkip($e, $request)) {
            return null;
        }

        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

        if (! in_array($status, config('oops.statuses', []), true)) {
            return null;
        }

        if (config('app.debug') && ! config('oops.force')) {
            return null;
        }

        ['view' => $view, 'data' => $data] = $this->app->make(ErrorPageComposer::class)->compose($status);

        $this->logReference($e, $status, $data['reference']);

        return response()->view($view, $data, $status, $e instanceof HttpExceptionInterface ? $e->getHeaders() : []);
    }

    /**
     * Logs a line carrying the same reference code shown to the visitor
     * (via "support" copy's "{ref}" token), so a code reported by a user
     * can be traced back to the actual exception and its stack trace.
     * 5xx logs at "error"; everything else (403/404/419/429 — expected,
     * user-driven outcomes rather than bugs) logs at "warning" to avoid
     * flooding error-level logs with routine traffic.
     */
    protected function logReference(Throwable $e, int $status, string $reference): void
    {
        $level = $status >= 500 ? 'error' : 'warning';

        Log::{$level}("[{$reference}] ".get_class($e).': '.$e->getMessage(), [
            'oops_reference' => $reference,
            'status' => $status,
            'exception' => $e,
        ]);
    }

    protected function shouldSkip(Throwable $e, $request): bool
    {
        return $request->expectsJson()
            || $e instanceof AuthenticationException
            || $e instanceof ValidationException
            || $e instanceof HttpResponseException;
    }
}
