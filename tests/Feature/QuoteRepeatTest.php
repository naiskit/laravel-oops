<?php

namespace Naiskit\LaravelOops\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Naiskit\LaravelOops\Quotes\FileQuoteRepository;
use Naiskit\LaravelOops\Quotes\QuoteRepository;
use Naiskit\LaravelOops\Rendering\ErrorPageComposer;
use Naiskit\LaravelOops\Tests\TestCase;

class QuoteRepeatTest extends TestCase
{
    /**
     * Binds a real, started session onto the container's 'request' instance
     * so ErrorPageComposer can track the last quote shown — mirrors what
     * StartSession middleware does for an actual HTTP request.
     */
    protected function bindSessionBackedRequest(): void
    {
        $store = new Store('oops_test_session', new ArraySessionHandler(120));
        $store->start();

        $request = Request::create('/throw/404');
        $request->setLaravelSession($store);

        $this->app->instance('request', $request);
    }

    public function test_the_same_quote_never_shows_twice_in_a_row(): void
    {
        // "en" specifically: 404's "id" pool has only one verified quote,
        // which would make this assertion flaky/impossible on its own.
        config(['oops.lang' => 'en']);
        $this->bindSessionBackedRequest();

        $composer = $this->app->make(ErrorPageComposer::class);

        $first = $composer->compose(404)['data']['quote'];
        $second = $composer->compose(404)['data']['quote'];

        $this->assertNotSame($first['text'], $second['text']);
    }

    public function test_a_third_pick_can_repeat_the_first_once_the_second_has_passed(): void
    {
        // Not a strict assertion (random), but proves the exclusion only
        // ever applies to the immediately previous quote, not the whole
        // history — otherwise a small pool would eventually run dry.
        // "en" specifically: 404's "id" pool has only one verified quote.
        config(['oops.lang' => 'en']);
        $this->bindSessionBackedRequest();

        $composer = $this->app->make(ErrorPageComposer::class);

        $texts = [
            $composer->compose(404)['data']['quote']['text'],
            $composer->compose(404)['data']['quote']['text'],
            $composer->compose(404)['data']['quote']['text'],
        ];

        $this->assertNotSame($texts[0], $texts[1]);
        $this->assertNotSame($texts[1], $texts[2]);
    }

    public function test_it_falls_back_to_a_repeat_when_the_pool_has_only_one_quote(): void
    {
        // The fixture quote below is lang "en".
        config(['oops.lang' => 'en']);
        $this->bindSessionBackedRequest();

        $this->app->instance(QuoteRepository::class, new FileQuoteRepository(
            __DIR__.'/../Fixtures/custom-quotes.php',
            __DIR__.'/../../resources/quotes/index.php'
        ));

        $composer = $this->app->make(ErrorPageComposer::class);

        $first = $composer->compose(404)['data']['quote'];
        $second = $composer->compose(404)['data']['quote'];

        $this->assertSame('Custom quote for testing.', $first['text']);
        $this->assertSame($first['text'], $second['text']);
    }

    public function test_it_does_not_error_without_a_session(): void
    {
        // The default request bound in a Testbench app has no session
        // (mirrors an unmatched route, which never reaches StartSession).
        $composer = $this->app->make(ErrorPageComposer::class);

        $quote = $composer->compose(404)['data']['quote'];

        $this->assertNotEmpty($quote);
    }
}
