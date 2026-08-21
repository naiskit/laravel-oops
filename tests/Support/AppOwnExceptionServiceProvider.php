<?php

namespace Naiskit\LaravelOops\Tests\Support;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Stands in for an app's own custom exception rendering (e.g. registered
 * via bootstrap/app.php's withExceptions()), to prove the package defers
 * to whatever already handled the exception instead of overriding it.
 */
class AppOwnExceptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->make(ExceptionHandler::class)->renderable(
            fn (Throwable $e, $request) => $request->is('throw/404')
                ? new Response('Custom app 404 response', 404)
                : null
        );
    }
}
