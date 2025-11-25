<?php

return [

    'users' => [
        'superAdmin' => [
            'enabled'   => env('SEED_SUPER_ADMIN_USER_ENABLED', false),
            'name'      => env('SEED_SUPER_ADMIN_USER_NAME', 'Ota'),
            'email'     => env('SEED_SUPER_ADMIN_USER_EMAIL', 'ota@example.com'),
            'password'  => env('SEED_SUPER_ADMIN_USER_PASSWORD', 'password'),
            'role'      => env('SEED_SUPER_ADMIN_USER_ROLE', 'superuser'),
        ],
        'regular' => [
            'enabled'   => env('SEED_REGULAR_USER_ENABLED', false),
            'name'      => env('SEED_REGULAR_USER_NAME', 'Regular User'),
            'email'     => env('SEED_REGULAR_USER_EMAIL', 'user@example.com'),
            'password'  => env('SEED_REGULAR_USER_PASSWORD', 'password'),
            'role'      => env('SEED_REGULAR_USER_ROLE', 'user'),
        ],
    ],
];
