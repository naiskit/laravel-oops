<?php

return [
    'statuses' => [
        403 => [
            'message' => 'Whoa, this door\'s locked! This room is VIP-only, and your name isn\'t on the list just yet.',
            'support' => 'Think you should be on the list? Give the administrator a nudge.',
        ],
        404 => [
            'message' => 'Oops, you\'re lost! This page is playing hide-and-seek, and so far it\'s winning.',
            'support' => 'Sure it should be here? Tell the administrator so they can help look.',
        ],
        419 => [
            'message' => 'You were away so long your session fell fast asleep. A quick reload will wake it up.',
            'support' => 'Reload the page and try again. Still grumpy? Call the administrator.',
        ],
        429 => [
            'message' => 'Whoa, easy there! Your fingers are fast, and the server is out of breath. Give it a moment.',
            'support' => 'Wait a moment, then try again. Still stuck? Call the administrator.',
        ],
        500 => [
            'message' => 'Oops, the server tripped! Don\'t worry, it\'s not you. It\'s just picking up the pieces it dropped.',
            'support' => 'If it keeps happening, send this code to the administrator so they can catch the culprit: {ref}',
        ],
        503 => [
            'message' => 'We\'re tidying the place up to make it even better. Doors open again soon!',
            'support' => 'The administrator will announce when we\'re back. Hang tight!',
        ],
    ],

    // Used for any status code not listed above.
    'default' => [
        'message' => 'Hmm, something went off-script. Don\'t worry, we\'re looking into it.',
        'support' => 'If it keeps happening, send this code to the administrator: {ref}',
    ],

    'back_home' => 'Take Me Home',
    'go_back' => 'Back Up',
];
