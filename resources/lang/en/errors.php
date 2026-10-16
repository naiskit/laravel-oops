<?php

/*
 * Error titles, the same for every tone so the visitor always knows
 * exactly what happened. Tones only change the text below the title
 * (see formal.php, casual.php, fun.php).
 */
return [
    'statuses' => [
        403 => 'Access Denied',
        404 => 'Page Not Found',
        419 => 'Session Expired',
        429 => 'Too Many Requests',
        500 => 'Server Error',
        503 => 'Under Maintenance',
    ],

    // Used for any status code not listed above.
    'default' => 'An Error Occurred',

    'error_label' => 'Error',
];
