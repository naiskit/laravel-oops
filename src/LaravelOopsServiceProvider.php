<?php

namespace Naiskit\LaravelOops;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Naiskit\LaravelOops\Console\PreviewCommand;
use Naiskit\LaravelOops\Rendering\ErrorPageComposer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class LaravelOopsServiceProvider extends ServiceProvider
{
    /**
     * HTTP status codes this package intercepts. Anything that isn't an
     * HttpException (e.g. a random uncaught error) counts as 500.
     */
    public const STATUSES = [403, 404, 419, 429, 500, 503];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oops.php', 'oops');

        $this->app->singleton(ErrorPageComposer::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'oops');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'oops');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/oops.php' => config_path('oops.php'),
            ], 'oops-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/oops'),
            ], 'oops-views');

            $this->publishes([
                __DIR__.'/../resources/lang' => $this->app->langPath('vendor/oops'),
            ], 'oops-lang');

            $this->commands([PreviewCommand::class]);
        }

        if (config('oops.enabled')) {
            $this->app->make(ExceptionHandler::class)->renderable(
                fn (Throwable $e, $request) => $this->renderOopsPage($e, $request)
            );
        }
    }

    protected function renderOopsPage(Throwable $e, $request): ?Response
    {
        if ($this->shouldSkip($e, $request)) {
            return null;
        }

        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

        // APP_DEBUG keeps Laravel's own detailed error page; use
        // `php artisan oops:preview` to look at this page locally.
        if (! in_array($status, self::STATUSES, true) || config('app.debug')) {
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
