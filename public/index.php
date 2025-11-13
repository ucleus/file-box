<?php

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = __DIR__ . '/../src/';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load environment variables
require_once __DIR__ . '/../src/Utils/Env.php';
Utils\Env::load(__DIR__ . '/../.env');

// Set security headers
$securityConfig = require __DIR__ . '/../config/security.php';
foreach ($securityConfig['headers'] as $header => $value) {
    header("$header: $value");
}

// Get request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove leading slash
$requestUri = trim($requestUri, '/');

// Router
try {
    // Public delivery routes
    if (preg_match('#^dl/([a-zA-Z0-9]+)$#', $requestUri, $matches)) {
        $token = $matches[1];
        $controller = new Controllers\DeliveryController();
        $controller->show($token);
        exit;
    }

    if (preg_match('#^dl/([a-zA-Z0-9]+)/preview/(.+)$#', $requestUri, $matches)) {
        $token = $matches[1];
        $filename = $matches[2];
        $controller = new Controllers\DeliveryController();
        $controller->preview($token, $filename);
        exit;
    }

    if (preg_match('#^dl/([a-zA-Z0-9]+)/download/(.+)$#', $requestUri, $matches)) {
        $token = $matches[1];
        $filename = $matches[2];
        $controller = new Controllers\DeliveryController();
        $controller->downloadFile($token, $filename);
        exit;
    }

    if (preg_match('#^dl/([a-zA-Z0-9]+)/download-all$#', $requestUri, $matches)) {
        $token = $matches[1];
        $controller = new Controllers\DeliveryController();
        $controller->downloadAll($token);
        exit;
    }

    if (preg_match('#^dl/([a-zA-Z0-9]+)/tweak$#', $requestUri, $matches) && $requestMethod === 'POST') {
        $token = $matches[1];
        $controller = new Controllers\DeliveryController();
        $controller->requestTweak($token);
        exit;
    }

    // Admin authentication routes
    if ($requestUri === 'admin/login') {
        $controller = new Controllers\AuthController();
        $controller->showLogin();
        exit;
    }

    if ($requestUri === 'admin/otp/request' && $requestMethod === 'POST') {
        $controller = new Controllers\AuthController();
        $controller->requestOTP();
        exit;
    }

    if ($requestUri === 'admin/otp/verify' && $requestMethod === 'POST') {
        $controller = new Controllers\AuthController();
        $controller->verifyOTP();
        exit;
    }

    if ($requestUri === 'admin/logout') {
        $controller = new Controllers\AuthController();
        $controller->logout();
        exit;
    }

    // Admin dashboard routes (require authentication)
    if ($requestUri === 'admin' || $requestUri === 'admin/') {
        $controller = new Controllers\AdminController();
        $controller->dashboard();
        exit;
    }

    if ($requestUri === 'admin/deliveries/new') {
        $controller = new Controllers\AdminController();
        $controller->showNewDelivery();
        exit;
    }

    if ($requestUri === 'admin/deliveries/create' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->createDelivery();
        exit;
    }

    if ($requestUri === 'admin/uploads' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->uploadFiles();
        exit;
    }

    if ($requestUri === 'admin/deliveries/email' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->sendDeliveryEmail();
        exit;
    }

    if ($requestUri === 'admin/deliveries/pause' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->pauseDelivery();
        exit;
    }

    if ($requestUri === 'admin/deliveries/resume' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->resumeDelivery();
        exit;
    }

    if ($requestUri === 'admin/deliveries/expire' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->expireDelivery();
        exit;
    }

    if ($requestUri === 'admin/deliveries/regenerate-token' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->regenerateToken();
        exit;
    }

    if ($requestUri === 'admin/deliveries/repackage-zip' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->repackageZip();
        exit;
    }

    if ($requestUri === 'admin/deliveries/delete' && $requestMethod === 'POST') {
        $controller = new Controllers\AdminController();
        $controller->deleteDelivery();
        exit;
    }

    // Home page - redirect to admin login
    if ($requestUri === '' || $requestUri === '/') {
        header('Location: /admin/login');
        exit;
    }

    // 404 - Not found
    http_response_code(404);
    require __DIR__ . '/../src/Views/public/invalid.php';

} catch (Exception $e) {
    // Error handling
    error_log($e->getMessage());
    http_response_code(500);
    echo 'An error occurred. Please try again later.';
}
