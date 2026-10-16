<?php

namespace Naiskit\LaravelOops\Tests;

use Naiskit\LaravelOops\LaravelOopsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelOopsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Most tests assert Indonesian copy; LangResolutionTest covers the
        // actual "en" default and fallback.
        $app['config']->set('oops.lang', 'id');

        // Every rendered error page now logs a reference line — keep the
        // suite from writing to a real log file on every test run.
        $app['config']->set('logging.default', 'null');
    }
}
