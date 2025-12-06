<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Language Lines
    |--------------------------------------------------------------------------
    |
    */
    'welcome' => [
        'subject'   => 'Welcome to :appname 🎉🎉',
        'greeting'  => '👋 Welcome, :username 🎉',
        'msg_upper' => 'Thanks for registering at :appname.',
        'btn'       => 'Go to Dashboard',
        'msg_lower' => 'If you have any questions, just reply to this email.',
        'goodbye'   => 'Cheers! 🙏,',
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
        'subject'  => 'We\'re sorry to see you go...',
        'greeting' => 'Hello :username,',
        'message'  => 'We are very sorry to see you go. We wanted to let you know that your account has been deleted. Thank you for the time we shared.',
        'goodbye'  => 'We will miss you!',
    ],
];
