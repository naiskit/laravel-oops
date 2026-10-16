<?php

return [
    'statuses' => [
        403 => [
            'message' => 'Looks like you don\'t have permission to open this page.',
            'support' => 'If you think you should have access, just let the administrator know.',
        ],
        404 => [
            'message' => 'The page you\'re looking for may have moved, or never existed.',
            'support' => 'If you\'re sure this page should be here, just let the administrator know.',
        ],
        419 => [
            'message' => 'This page has been open for a while. Try refreshing it.',
            'support' => 'Reload the page and try again. Still stuck? Let the administrator know.',
        ],
        429 => [
            'message' => 'Slow down for a moment, then try again.',
            'support' => 'Wait a bit, then try again. Still stuck? Let the administrator know.',
        ],
        500 => [
            'message' => 'Not your fault. Our team is already on it.',
            'support' => 'If it keeps happening, let the administrator know and include this code: {ref}',
        ],
        503 => [
            'message' => 'We\'re making a few improvements. Back to normal shortly.',
            'support' => 'The administrator will let you know when things are back to normal.',
        ],
    ],
    'default' => [
        'message' => 'Something didn\'t go as expected.',
        'support' => 'If it keeps happening, let the administrator know and include this code: {ref}',
    ],
    'back_home' => 'Back to Home',
    'go_back' => 'Go Back',
];
