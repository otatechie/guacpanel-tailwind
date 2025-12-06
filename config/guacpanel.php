<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GuacPanel App Settings
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SEO/META Settings
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'description'   => env('APP_DESCRIPTION', 'A lightweight, customizable admin dashboard built with Laravel, Inertia, Vue, and Tailwind CSS.'),
        'keywords'      => env('APP_KEYWORDS', 'Laravel, Inertia.js, Vue.js, Tailwind CSS, Admin Dashboard, SaaS'),
        'author'        => env('APP_AUTHOR', 'Your Name'),
        'theme-color'   => env('APP_THEME_COLOR', '#3B82F6'),
    ],

    /*
    |--------------------------------------------------------------------------
    | User Account Settings
    |--------------------------------------------------------------------------
    */
    'user' => [
        'account' => [
            'deactivate_enabled'  => env('APP_USER_DEACTIVATE_ACCOUNT_ENABLED', true),
            'delete_enabled'      => env('APP_USER_DELETE_ACCOUNT_ENABLED', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | App Master Permissions Settings :: Allows Gate in AuthServiceProvider
    | This overrides all permissions checks and auth middleware
    | Best to check on permissions in most cases
    | Use with extreme caution and intention
    |--------------------------------------------------------------------------
    */
    'allow_superuser_override' => env('APP_ALLOW_SUPERUSER_OVERRIDE', false),

    /*
    |--------------------------------------------------------------------------
    | App Auth/Fortify Settings :: Here for parts of the app to reference
    |--------------------------------------------------------------------------
    */
    'registration_enabled'          => env('APP_REGISTRATION_ENABLED', true),
    'password_reset_enabled'        => env('APP_PW_RESET_ENABLED', true),
    'email_verification_enabled'    => env('APP_EMAIL_VERIFICATION_ENABLED', false),
    'update_profile_enabled'        => env('APP_UPDATE_PROFILE_ENABLED', false),
    'update_password_enabled'       => env('APP_UPDATE_PW_ENABLED', false),
    'mfa_enabled'                   => env('APP_MFA_ENABLED', false),
    'auto_login_after_register'     => env('APP_AUTO_LOGIN_AFTER_REGISTER', false),
];
