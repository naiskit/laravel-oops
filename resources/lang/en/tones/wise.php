<?php

// Calm and reflective: a short quote per status, then what happened and
// what to do. Quotes are quoted exactly from their original (or a
// standard translation). No paraphrases, no "Anonymous".

return [
    'statuses' => [
        403 => [
            'quote' => [
                'text' => 'There is a remedy for every wrong, and every wall is a gate.',
                'author' => 'Ralph Waldo Emerson',
            ],
            'message' => 'This page is open only to those with permission. For now, this wall stays a wall.',
            'support' => 'If you believe you should have access, ask the administrator.',
        ],
        404 => [
            'quote' => [
                'text' => 'Not all those who wander are lost.',
                'author' => 'J.R.R. Tolkien',
            ],
            'message' => 'But this page is. It may have moved, or the address may be slightly off.',
            'support' => 'Check the address, or start again from the home page.',
        ],
        419 => [
            'quote' => [
                'text' => 'The Moving Finger writes; and, having writ, moves on.',
                'author' => 'Omar Khayyám',
            ],
            'message' => 'Time moved on, and your session expired while the page stayed open.',
            'support' => 'Reload the page and pick up from there.',
        ],
        429 => [
            'quote' => [
                'text' => 'Haste makes waste.',
                'author' => 'Benjamin Franklin',
            ],
            'message' => 'Too many requests arrived in a short time. The system needs a brief pause.',
            'support' => 'Wait a moment, then try again.',
        ],
        500 => [
            'quote' => [
                'text' => 'Ever tried. Ever failed. No matter. Try again. Fail again. Fail better.',
                'author' => 'Samuel Beckett',
            ],
            'message' => 'Something failed on our side, not yours, and we are already working on it.',
            'support' => 'If it keeps happening, send this code to the administrator: {ref}',
        ],
        503 => [
            'quote' => [
                'text' => 'To every thing there is a season, and a time to every purpose under the heaven.',
                'author' => 'Ecclesiastes 3:1',
            ],
            'message' => 'This is our season for maintenance, so the service can come back better.',
            'support' => 'We will be back shortly. Please try again in a little while.',
        ],
    ],

    // Used for any status code not listed above.
    'default' => [
        'quote' => [
            'text' => 'Although the world is full of suffering, it is full also of the overcoming of it.',
            'author' => 'Helen Keller',
        ],
        'message' => 'Something did not go as it should, and we are working on it.',
        'support' => 'If it keeps happening, send this code to the administrator: {ref}',
    ],

    'back_home' => 'Back to Home',
    'go_back' => 'Previous Page',
];
