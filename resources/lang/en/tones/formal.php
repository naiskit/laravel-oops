<?php

return [
    'statuses' => [
        403 => [
            'message' => 'We\'re sorry, you do not currently have permission to access this page.',
            'support' => 'If you believe you need access, please contact the administrator.',
        ],
        404 => [
            'message' => 'We\'re sorry, the page you are looking for could not be found. It may have been moved or is no longer available.',
            'support' => 'Please check the address you entered, or return to the home page.',
        ],
        419 => [
            'message' => 'For your security, your session has expired because this page was open for some time.',
            'support' => 'Please reload the page and try again.',
        ],
        429 => [
            'message' => 'We\'re sorry, we have received too many requests in a short period of time.',
            'support' => 'Please wait a moment before trying again.',
        ],
        500 => [
            'message' => 'We apologize for the inconvenience. Our system encountered an issue, and we are working to resolve it.',
            'support' => 'If the issue persists, please contact the administrator and include the following reference code: {ref}',
        ],
        503 => [
            'message' => 'We are performing maintenance to improve our service. Thank you for your patience.',
            'support' => 'The service will be available again shortly. Please try again in a few moments.',
        ],
    ],

    // Used for any status code not listed above.
    'default' => [
        'message' => 'We\'re sorry, your request could not be processed at this time.',
        'support' => 'If the issue persists, please contact the administrator and include the following reference code: {ref}',
    ],

    'back_home' => 'Back to Home',
    'go_back' => 'Previous Page',
];
