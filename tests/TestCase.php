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
        // Most tests only care about behavior unrelated to locale, so pin
        // a predictable default here. LocaleResolutionTest overrides this
        // per test to exercise the actual id/en resolution logic.
        $app['config']->set('oops.locale', 'id');

        // Every rendered error page now logs a reference line — keep the
        // suite from writing to a real log file on every test run.
        $app['config']->set('logging.default', 'null');
    }
}
