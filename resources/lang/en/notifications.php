<?php

return [

    /**
     * --------------------------------------------------------------------------
     *  Non out the box application language lines
     * --------------------------------------------------------------------------.
     *
     * USAGE:
     *
     * __('notifications.register.pw_success_auto_login_enabled')
     * __('notifications.register.pw_success_auto_login_disabled_activation_enabled')
     * __('notifications.register.pw_success_auto_login_disabled_activation_disabled')
     * __('notifications.register.sm_registration_successful')
     *
     * __('notifications.login.sm_login_successful')
     * __('notifications.login.sm_unable_to_login_with')
     *
     * __('notifications.errors.social_session_invalid')
     * __('notifications.errors.no_file_uploaded')
     * __('notifications.errors.su_cannot_be_deleted')
     * __('notifications.errors.su_status_cannot_be_modified')
     * __('notifications.errors.user_cannot_be_disabled_and_force_change_pw')
     *
     * __('notifications.admin.settings_updated_successfully')
     * __('notifications.admin.all_users_deleted_successfully')
     * __('notifications.admin.user_account_updated_successfully')
     * __('notifications.admin.user_deleted_successully')
     * __('notifications.admin.new_user_created_successfully')
     */
    'register' => [
        'pw_success_auto_login_enabled'                         => 'Great! Your account has been created successfully.',
        'pw_success_auto_login_disabled_activation_enabled'     => 'Please verify your email by clicking the activation link we have sent you.',
        'pw_success_auto_login_disabled_activation_disabled'    => 'Welcome, you may now login!',
        'sm_registration_successful'                            => ' Registration Successful',
    ],

    'login' => [
        'sm_login_successful'       => ' Login Successful',
        'sm_unable_to_login_with'   => 'Unable to login with ',
    ],

    'errors' => [
        'sm_session_invalid'                            => 'Your social login session is invalid or expired. Please try again.',
        'no_file_uploaded'                              => 'No file uploaded',
        'su_cannot_be_deleted'                          => 'Super user cannot be deleted.',
        'su_status_cannot_be_modified'                  => 'Superuser account status and role cannot be modified.',
        'user_cannot_be_disabled_and_force_change_pw'   => 'User cannot be both disabled and forced to change password.',
    ],

    'admin' => [
        'settings_updated_successfully'     => 'Settings updated successfully.',
        'all_users_deleted_successfully'    => 'All deleted users permanently destroyed.',
        'user_account_updated_successfully' => 'User account updated successfully',
        'user_deleted_successully'          => 'User deleted successfull',
        'new_user_created_successfully'     => 'New user account created successfully.',
    ],

    'verify' => [
        'already' => 'Your email address is already verified',
        'success' => 'Thanks! Your email address has been verified',
    ],
];
