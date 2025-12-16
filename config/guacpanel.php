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
    | Admin Type Settings
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'support_email' => env('APP_HELP_EMAIL', 'support@example.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | User Account Settings
    |--------------------------------------------------------------------------
    */
    'user' => [
        'account' => [
            'deactivate_enabled'    => env('APP_USER_DEACTIVATE_ACCOUNT_ENABLED', true),
            'delete_enabled'        => env('APP_USER_DELETE_ACCOUNT_ENABLED', true),
            'restore_enabled'       => env('APP_USER_RESTORE_ACCOUNT_ENABLED', true),
            'days_to_restore'       => env('APP_USER_DAYS_TO_RESTORE_ACCOUNT', 60),
            'restore_route'         => 'uac.restore',
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
    'update_profile_enabled'        => env('APP_UPDATE_PROFILE_ENABLED', false),
    'update_password_enabled'       => env('APP_UPDATE_PW_ENABLED', false),
    'mfa_enabled'                   => env('APP_MFA_ENABLED', false),
    'auto_login_after_register'     => env('APP_AUTO_LOGIN_AFTER_REGISTER', false),
    'email_verification_enabled'    => env('APP_EMAIL_VERIFICATION_ENABLED', false),
    'auth_required_to_verify_email' => env('APP_AUTH_REQUIRED_TO_VERIFY_EMAIL', false),

    /*
    |--------------------------------------------------------------------------
    | App Demo Settings
    |--------------------------------------------------------------------------
    */
    'demo' => [
        'enabled'   => env('APP_DEMO_ENABLED', false),
        'login'     => [
            'username' => env('APP_DEMO_LOGIN_USERNAME', null),
            'password' => env('APP_DEMO_LOGIN_PASSWORD', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled'                   => env('APP_NOTIFICATIONS_ENABLED', true),
        'in_demo'                   => env('APP_NOTIFICATIONS_IN_DEMO_MODE', true),
        'auto_cleanup_deleted'      => env('APP_NOTIFICATIONS_AUTO_CLEANUP_DELETED_ENABLED', true),
        'auto_cleanup_deleted_days' => env('APP_NOTIFICATIONS_AUTO_CLEANUP_DELETED_DAYS', 60),
        'auto_clean_send_email'     => env('APP_NOTIFICATIONS_AUTO_CLEANUP_SEND_EMAIL', false),
        'auto_clean_send_email_to'  => env('APP_NOTIFICATIONS_AUTO_CLEANUP_SEND_EMAIL_TO', null),
    ],
];
