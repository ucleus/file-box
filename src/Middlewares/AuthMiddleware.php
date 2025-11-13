<?php

namespace Middlewares;

use Utils\Response;

class AuthMiddleware
{
    public static function check()
    {
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['authenticated'])) {
            Response::redirect('/admin/login');
        }

        // Check session timeout
        $config = require __DIR__ . '/../../config/app.php';
        $lifetime = $config['session_lifetime'];

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $lifetime) {
            session_unset();
            session_destroy();
            Response::redirect('/admin/login?timeout=1');
        }

        $_SESSION['last_activity'] = time();

        return true;
    }

    public static function guest()
    {
        session_start();

        if (isset($_SESSION['user_id']) && isset($_SESSION['authenticated'])) {
            Response::redirect('/admin');
        }

        return true;
    }

    public static function login($userId)
    {
        session_start();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        $_SESSION['authenticated'] = true;
        $_SESSION['last_activity'] = time();
    }

    public static function logout()
    {
        session_start();
        session_unset();
        session_destroy();
    }

    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']) && isset($_SESSION['authenticated']);
    }
}
