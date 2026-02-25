<?php

// Opcional: use DB_TLS_ALLOW_INVALID=true no .env apenas para testes se der erro de TLS com Atlas (ex.: PHP 8.5 + OpenSSL)
$mongodbDsn = env('DB_URI');
if ($mongodbDsn && env('DB_TLS_ALLOW_INVALID', false)) {
    $separator = str_contains($mongodbDsn, '?') ? '&' : '?';
    $mongodbDsn .= $separator . 'tlsAllowInvalidCertificates=true';
}

return [
    'default' => env('DB_CONNECTION', 'mongodb'),
    
    'connections' => [
        'mongodb' => [
            'driver' => 'mongodb',
            'dsn' => $mongodbDsn,
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE', 'laravel_orders'),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'options' => [
                'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'),
                'serverSelectionTimeoutMS' => 5000,
            ],
        ],
    ],
    
    'migrations' => 'migrations',
];
