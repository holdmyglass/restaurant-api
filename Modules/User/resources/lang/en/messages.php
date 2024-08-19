<?php

return [
    'register' => [
        'identity_required' => 'How would you prefer to register?',
        'identity_enum' => 'Registration options are limited to email or phone.',
        'full_name_required' => "Hold on! What's your full name?",
        'email_required' => "Let's roll! Your email address?",
        'email_email' => 'Please provide a valid email.',
        'email_unique' => 'This email is already registered.',
        'phone_required' => 'Continue with your phone number.',
        'phone_unique' => 'This phone number is already registered.',
        'password_required' => 'Secure your account with a password.',
        'password_min' => 'Password must be at least :min characters.',
        'password_regex' => 'Mix it up! Use uppercase, lowercase, digits, and symbols.',
        'password_confirmed' => "Oops! Passwords don't match.",
        'terms_accepted' => 'Agree to the terms to join the fun!',
        'success' => 'Registration successful!',
    ],
    'email' => [
        'subject_email_verification_code' => 'Verify Your Account ',
        'subject_email_verification_link' => 'Verify Your Account ',
        'subject_password_reset_code' => 'Reset Your Account Password',
    ],
];
