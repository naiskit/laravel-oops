<?php

// Short and to the point: one sentence each, no apologies or small talk.
// Suits internal tools and dashboards people use every day.

return [
    'statuses' => [
        403 => [
            'message' => 'You don\'t have access to this page.',
            'support' => 'Need access? Contact the administrator.',
        ],
        404 => [
            'message' => 'This page is not available.',
            'support' => 'Check the address or go back to the home page.',
        ],
        419 => [
            'message' => 'Your session has expired.',
            'support' => 'Reload the page and try again.',
        ],
        429 => [
            'message' => 'Too many requests in a short time.',
            'support' => 'Wait a moment, then try again.',
        ],
        500 => [
            'message' => 'Something went wrong on the server.',
            'support' => 'If it persists, report this code: {ref}',
        ],
        503 => [
            'message' => 'The service is under maintenance.',
            'support' => 'Try again in a few minutes.',
        ],
    ],

    // Used for any status code not listed above.
    'default' => [
        'message' => 'The request could not be completed.',
        'support' => 'If it persists, report this code: {ref}',
    ],

    'back_home' => 'Home',
    'go_back' => 'Back',
];
