<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    'allowed_methods' => ['*'],

 
    'allowed_origins' => [
        'http://localhost:5173', // URL của VueJS
        'http://localhost:3000', // URL của Node.js Gateway
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, 
];