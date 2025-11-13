<?php

namespace Utils;

class Response
{
    public static function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function error($message, $status = 400)
    {
        self::json(['error' => $message], $status);
    }

    public static function success($message, $data = [])
    {
        self::json(array_merge(['success' => $message], $data));
    }

    public static function redirect($url, $status = 302)
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    public static function notFound($message = 'Page not found')
    {
        http_response_code(404);
        require __DIR__ . '/../Views/public/invalid.php';
        exit;
    }

    public static function unauthorized($message = 'Unauthorized')
    {
        http_response_code(401);
        echo $message;
        exit;
    }

    public static function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
    }

    public static function download($filepath, $filename)
    {
        if (!file_exists($filepath)) {
            self::notFound();
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        readfile($filepath);
        exit;
    }
}
