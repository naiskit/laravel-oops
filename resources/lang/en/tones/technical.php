<?php

// For admin panels and tools used by IT or developers: what the status
// means and what to check. Static copy only, never exception details;
// the real cause is in the application log under the reference code.

return [
    'statuses' => [
        403 => [
            'message' => 'The request was authenticated but not authorized. The current user lacks the role or permission this route requires.',
            'support' => 'Check the user\'s roles and the policy or gate for this route.',
        ],
        404 => [
            'message' => 'No route or resource matches this URL. The route may not exist, or the record it points to was not found.',
            'support' => 'Check the URL, the route definition, and that the requested ID exists.',
        ],
        419 => [
            'message' => 'The CSRF token is missing or no longer valid, usually because the session expired before the form was submitted.',
            'support' => 'Reload the page to get a fresh token. If it keeps happening, check the session lifetime and driver.',
        ],
        429 => [
            'message' => 'The rate limit for this route was exceeded. Further requests are rejected until the limit window resets.',
            'support' => 'Wait for the window to reset, or review the rate limiter configured for this route.',
        ],
        500 => [
            'message' => 'An unhandled exception occurred while processing the request. The full details are in the application log.',
            'support' => 'Search the application log for this reference code: {ref}',
        ],
        503 => [
            'message' => 'The application is in maintenance mode or temporarily unavailable.',
            'support' => 'Wait for maintenance to finish, or run "php artisan up" if it should already be live.',
        ],
    ],

    // Used for any status code not listed above.
    'default' => [
        'message' => 'The request ended with an HTTP error. The full details are in the application log.',
        'support' => 'Search the application log for this reference code: {ref}',
    ],

    'back_home' => 'Home',
    'go_back' => 'Back',
];
