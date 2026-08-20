<?php

namespace Naiskit\LaravelOops\Rendering;

use Illuminate\Contracts\Foundation\Application;
use Naiskit\LaravelOops\Quotes\QuoteRepository;

/**
 * Builds the view name + data for a status code — shared by the exception
 * renderer (LaravelOopsServiceProvider) and the `oops:preview` command, so
 * both always render exactly the same page for a given status.
 */
class ErrorPageComposer
{
    public function __construct(
        protected Application $app,
        protected QuoteRepository $quotes
    ) {}

    /**
     * @return array{view: string, data: array<string, mixed>}
     */
    public function compose(int $status): array
    {
        $locale = $this->resolveLocale();
        $copy = $this->resolveCopy($locale, $status);

        return [
            'view' => $this->resolveView($status),
            'data' => [
                'code' => $status,
                'title' => $copy['title'],
                'message' => $copy['message'],
                'locale' => $locale,
                'backHomeLabel' => config("oops.ui.{$locale}.back_home") ?? config('oops.ui.id.back_home'),
                'unknownAuthorLabel' => config("oops.ui.{$locale}.unknown_author") ?? config('oops.ui.id.unknown_author'),
                'quoteLead' => config("oops.ui.{$locale}.quote_lead") ?? config('oops.ui.id.quote_lead'),
                'quote' => $this->quotes->random(
                    $status,
                    config('oops.quote_languages', []),
                    config('oops.quote_genres', [])
                ),
            ],
        ];
    }

    public function resolveView(int $status): string
    {
        if ($view = config('oops.view')) {
            return $view;
        }

        $view = "oops::{$status}";

        return $this->app->make('view')->exists($view) ? $view : 'oops::general';
    }

    public function resolveLocale(): string
    {
        $locale = config('oops.locale') ?: $this->app->getLocale();

        return array_key_exists($locale, config('oops.messages', [])) ? $locale : 'id';
    }

    public function resolveCopy(string $locale, int $status): array
    {
        return config("oops.messages.{$locale}.{$status}")
            ?? config("oops.messages.id.{$status}")
            ?? config("oops.default_message.{$locale}")
            ?? config('oops.default_message.id');
    }
}
