<?php

return [
    'default' => env('DB_CONNECTION', 'pgsql'),

    'connections' => [
        // We explicitly define ONLY pgsql. 
        // To stop Laravel from auto-merging defaults, we leave the others out.
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'househub_prod'),
            'username' => env('DB_USERNAME', 'houseadmin'),
            'password' => env('DB_PASSWORD', 'jh61:5bgQtHk'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
    ],
    
    // This tells Laravel: "Don't look elsewhere."
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];