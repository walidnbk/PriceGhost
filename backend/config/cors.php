<?php

return [
    'paths' => ['*', 'api/*', 'sanctum/csrf-cookie'], // Ajoute '*' ici
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Très important : autorise tout le monde
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];