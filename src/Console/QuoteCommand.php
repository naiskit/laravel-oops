<?php

namespace Naiskit\LaravelOops\Console;

use Illuminate\Console\Command;
use Naiskit\LaravelOops\Quotes\QuoteRepository;

class QuoteCommand extends Command
{
    protected $signature = 'oops:quote
        {status=500 : HTTP status code, e.g. 404, 403, 419, 429, 500, 503}
        {--lang=* : Filter by language, e.g. --lang=id --lang=en}
        {--genre=* : Filter by genre, e.g. --genre=humor --genre=wise}';

    protected $description = 'Show a random quote matching a given error type, language, and genre';

    public function handle(QuoteRepository $quotes): int
    {
        $status = (int) $this->argument('status');

        $quote = $quotes->random($status, $this->option('lang'), $this->option('genre'));

        if (empty($quote)) {
            $this->warn("No quotes found for status {$status}.");

            return self::FAILURE;
        }

        $this->line('"'.$quote['text'].'"');
        $this->line('— '.($quote['author'] ?? 'Unknown').(! empty($quote['source']) ? ', '.$quote['source'] : ''));

        return self::SUCCESS;
    }
}
