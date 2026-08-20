<?php

// A minimal fixture used by FileQuoteRepositoryTest to verify that a
// custom quotes_path overrides the package's bundled quotes.

return [
    404 => [
        [
            'text' => 'Custom quote for testing.',
            'author' => 'Tester',
            'source' => null,
            'lang' => 'en',
            'genre' => 'humor',
            'meaning' => 'A fixture used in tests.',
        ],
    ],
];
