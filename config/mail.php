<?php

return [
    'from_email' => getenv('MAIL_FROM') ?: 'noreply@ucleus.com',
    'from_name' => 'Ucleus',

    // SMTP settings (configure for Hostinger)
    'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.hostinger.com',
    'smtp_port' => getenv('SMTP_PORT') ?: 587,
    'smtp_username' => getenv('SMTP_USERNAME') ?: '',
    'smtp_password' => getenv('SMTP_PASSWORD') ?: '',
    'smtp_secure' => getenv('SMTP_SECURE') ?: 'tls', // tls or ssl

    // OTP settings
    'otp_expires_minutes' => 10,
    'otp_length' => 6,
];
