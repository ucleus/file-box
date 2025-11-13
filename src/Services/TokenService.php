<?php

namespace Services;

class TokenService
{
    public static function generate($length = 32)
    {
        $config = require __DIR__ . '/../../config/security.php';
        $bytes = $config['token_bytes'];

        $token = bin2hex(random_bytes($bytes));
        return substr($token, 0, $length);
    }

    public static function generateOTP($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9);
        }
        return $otp;
    }

    public static function hash($value)
    {
        return hash('sha256', $value);
    }

    public static function verify($value, $hash)
    {
        return hash_equals($hash, self::hash($value));
    }
}
