<?php

namespace Utils;

class Validator
{
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($value)
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        return !empty($value);
    }

    public static function maxLength($value, $max)
    {
        return mb_strlen($value) <= $max;
    }

    public static function minLength($value, $min)
    {
        return mb_strlen($value) >= $min;
    }

    public static function numeric($value)
    {
        return is_numeric($value);
    }

    public static function fileExtension($filename, array $allowedExtensions)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $allowedExtensions);
    }

    public static function fileSize($size, $maxSize)
    {
        return $size <= $maxSize;
    }

    public static function date($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function sanitize($value)
    {
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function validateDelivery($data)
    {
        $errors = [];

        if (!self::required($data['client_name'] ?? '')) {
            $errors[] = 'Client name is required';
        }

        if (!self::required($data['project_name'] ?? '')) {
            $errors[] = 'Project name is required';
        }

        if (isset($data['client_email']) && $data['client_email'] !== '' && !self::email($data['client_email'])) {
            $errors[] = 'Invalid email address';
        }

        if (isset($data['max_downloads']) && !self::numeric($data['max_downloads'])) {
            $errors[] = 'Max downloads must be a number';
        }

        return $errors;
    }
}
