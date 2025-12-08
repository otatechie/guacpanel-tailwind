<?php

return [

    /**
     * --------------------------------------------------------------------------
     *  Non out the box application language lines
     * --------------------------------------------------------------------------.
     *
     * Usage Example:
     *
     * __('notifications.register.pw_success_auto_login_enabled')
     * __('notifications.account.disabled', ['email' => config('guacpanel.admin.support_email')])
     *
     * --------------------------------------------------------------------------
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

    'account' => [
        'disabled'              => 'Account currently disabled. Email <a href="mailto::email" class="underline hover:text-orange-800">:email</a> for help.',
        'locked'                => 'Account Locked. Email <a href="mailto::email" class="underline hover:text-orange-800">:email</a> for help.',
        'force_pw_change'       => 'You must change your password before continuing.',
        'two_factor_required'   => 'Two-factor authentication is required. Please enable it to continue.',
        'invalid_restore_link'  => 'The link is either invalid or has expired.',
        'deleted_successfully'  => 'Account has been deleted successfully 👋',
        'already_logged_in'     => 'Unable to complete action, you are currently logged in as :username',
        'account_restored'      => 'Welcome back 👋 Account restored successfully.',
    ],

    'general' => [
        'feature_disabled' => 'Feature disabled in the .env file',
    ],
];
