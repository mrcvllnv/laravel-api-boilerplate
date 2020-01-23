<?php

return [
    '200'   => ['message' => 'OK'],

    // Default HTTP Errors
    '400'   => ['message' => 'Bad request.'],
    '401'   => ['message' => 'Authorization required.'],
    '403'   => ['message' => 'Access to that resource is forbidden.'],
    '404'   => ['message' => 'The requested resource was not found.'],
    '405'   => ['message' => 'Method not allowed.'],
    '406'   => ['message' => 'Not acceptable response.'],
    '410'   => ['message' => 'The requested resource is gone and won’t be coming back.'],
    '415'   => ['message' => 'Unsupported media type.'],
    '422'   => ['message' => 'Validation error.'],
    '429'   => ['message' => 'Too many requests.'],
    '500'   => ['message' => 'There was an error on the server and the request could not be completed.'],
    '501'   => ['message' => 'Not implemented.'],
    '502'   => ['message' => 'Bad Gateway.'],
    '503'   => ['message' => 'The server is unavailable to handle this request right now.'],
    '521'   => ['message' => 'Web server is down.'],

    // Custom Errors
    '0'     => ['message' => 'Unknown error.'],

    // SQL exceptions
    '23000' => ['message' => 'Duplicate entry.'],
];