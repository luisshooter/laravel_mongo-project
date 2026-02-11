<?php

return [
    'default' => env('DB_CONNECTION', 'mongodb'),
    
    'connections' => [
        'mongodb' => [
            'driver' => 'mongodb',
            'dsn' => env('DB_URI'), // use para Atlas ou conexão por URI (ex: mongodb+srv://...)
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE', 'laravel_orders'),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'options' => [
                'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'),
            ],
        ],
    ],
    
    'migrations' => 'migrations',
];
