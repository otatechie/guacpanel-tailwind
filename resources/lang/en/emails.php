<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Language Lines
    |--------------------------------------------------------------------------
    |
    */
    'general' => [
        'greeting'  => 'Hi, :username 👋',
        'goodbye'   => 'Thank you,',
        'dashboard' => 'Go to Dashboard',
        'access'    => 'Access Your Account',
    ],

    'welcome' => [
        'subject'   => 'Welcome to :appname 🎉🎉',
        'greeting'  => '👋 Welcome, :username 🎉',
        'line_one'  => 'Thanks for registering at :appname.',
        'btn'       => 'Go to Dashboard',
        'line_two'  => 'If you have any questions, just reply to this email.',
        'goodbye'   => 'Cheers! 🙏',
    ],

    'welcome-verified' => [
        'subject'   => 'Welcome to :appname - Account Verified 🎉🎉',
        'greeting'  => '👋 Welcome, :username 🎉',
        'line_one'  => 'Your account is now verified. Welcome to :appname.',
        'btn'       => 'Go to Dashboard',
        'line_two'  => 'If you have any questions, just reply to this email.',
        'goodbye'   => 'Cheers! 🙏',
    ],

    'verify' => [
        'subject'       => 'Verify Email Address',
        'greeting'      => 'Hi, :username 👋',
        'msg_upper_one' => 'Thanks for signing up for :appname.',
        'msg_upper_two' => 'Please confirm your email to activate your account.',
        'btn'           => 'Verify Email Address',
        'msg_lower'     => 'If you did not create an account, no further action is required.',
        'goodbye'       => 'Thanks,',
    ],

    'verify_from_admin' => [
        'subject'       => 'Verify Email Address',
        'greeting'      => 'Hello, :username 👋',
        'msg_upper_one' => 'Please confirm your email to activate your account.',
        'btn'           => 'Verify Email Address',
        'msg_lower'     => 'If you have any questions, just reply to this email.',
        'goodbye'       => 'Thank you!,',
    ],

    'goodbye' => [
        'subject'           => 'We\'re sorry to see you go... 😞 ',
        'greeting'          => '👋 Hey there :username,',
        'common_line_one'   => 'We are very sorry to see you go. We wanted to let you know that your account has been successfully deleted per your request.',
        'restore_line_one'  => 'Your account can be restored for up to :days days.',
        'restore_btn_text'  => 'Restore Account',
        'common_line_two'   => 'All account data will be permanently destroyed on :date.',
        'common_line_three' => 'Thank you for the time we shared.',
        'goodbye'           => 'We will miss you! 🙏🙏🙏',
    ],

    'restored' => [
        'subject'   => 'Welcome back to :appname 🎉🎉🎉',
        'greeting'  => 'Hi, :username 👋',
        'line_one'  => 'Welcome back to :appname. Your account has been successfully restored on :date.',
        'btn'       => 'Go to Dashboard',
        'line_two'  => 'If you did not restore this account, change your password and contact customer service at <a href="mailto::email" class="underline hover:text-orange-800">:email</a>.',
        'goodbye'   => 'We are so happy to have you back! 🥰',
    ],

    'notifications_cleanup' => [
        'subject'   => ':appname: Notifications cleanup report',
        'greeting'  => 'Hi Admin 👋',
        'line_one'  => 'Your scheduled notifications cleanup has completed for :appname.',
        'deleted'   => 'Deleted notifications: :count',
        'cutoff'    => 'Notifications deleted on or before: :date',
        'days'      => 'Days threshold: :days',
        'goodbye'   => 'Thank you,',
    ],
];
