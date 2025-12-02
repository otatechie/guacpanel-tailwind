<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GuacPanel App SEO/META Settings
    |--------------------------------------------------------------------------
    |
    */

    'meta' => [
        'description'   => env('APP_DESCRIPTION', 'A lightweight, customizable admin dashboard built with Laravel, Inertia, Vue, and Tailwind CSS.'),
        'keywords'      => env('APP_KEYWORDS', 'Laravel, Inertia.js, Vue.js, Tailwind CSS, Admin Dashboard, SaaS'),
        'author'        => env('APP_AUTHOR', 'Your Name'),
        'theme-color'   => env('APP_THEME_COLOR', '#3B82F6'),
    ],

    'user' => [
        'account' => [
            'deactivate_enabled'  => env('USER_DEACTIVATE_ACCOUNT_ENABLED', true),
            'delete_enabled'    => env('USER_DELETE_ACCOUNT_ENABLED', true),
        ],
    ],
];
