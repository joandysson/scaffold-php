<?php
return [
    // Enable or disable CORS handling
    'enabled' => true,
    // Origins allowed to access the resources
    'allow_origin' => '*',
    // HTTP methods allowed for CORS requests
    'allow_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    // Request headers allowed
    'allow_headers' => ['Content-Type', 'Authorization'],
    // Headers exposed to the browser
    'expose_headers' => [],
    // Preflight cache duration in seconds
    'max_age' => 0,
    // Whether to allow credentials such as cookies
    'allow_credentials' => true,
];
