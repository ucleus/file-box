<?php

namespace Controllers;

use Models\Delivery;
use Models\Asset;
use Services\Logger;
use Services\Mailer;
use Middlewares\RateLimitMiddleware;
use Utils\Response;

class DeliveryController
{
    private $deliveryModel;
    private $assetModel;
    private $logger;
    private $config;

    public function __construct()
    {
        $this->deliveryModel = new Delivery();
        $this->assetModel = new Asset();
        $this->logger = new Logger();
        $this->config = require __DIR__ . '/../../config/app.php';
    }

    public function show($token)
    {
        RateLimitMiddleware::checkPageView();

        $delivery = $this->deliveryModel->findByToken($token);

        if (!$delivery) {
            $this->showInvalid();
            return;
        }

        if ($this->deliveryModel->isExpired($delivery)) {
            $this->showExpired();
            return;
        }

        if ($this->deliveryModel->isPaused($delivery)) {
            $this->showPaused();
            return;
        }

        // Get assets
        $assets = $this->assetModel->findByDeliveryId($delivery['id']);

        // Log page view
        $this->logger->logPageView(
            $delivery['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // Load view
        require __DIR__ . '/../Views/public/delivery.php';
    }

    public function preview($token, $filename)
    {
        $delivery = $this->deliveryModel->findByToken($token);

        if (!$delivery || $this->deliveryModel->isExpired($delivery)) {
            Response::notFound();
        }

        $asset = $this->assetModel->findByDeliveryAndFilename($delivery['id'], $filename);

        if (!$asset) {
            Response::notFound();
        }

        // Check if file type is previewable
        $previewExtensions = $this->config['preview_extensions'];
        $ext = strtolower(pathinfo($asset['filename'], PATHINFO_EXTENSION));

        if (!in_array($ext, $previewExtensions)) {
            Response::error('This file type cannot be previewed');
        }

        $filepath = $asset['file_path'];

        if (!file_exists($filepath)) {
            Response::notFound();
        }

        // Set appropriate content type
        $contentTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
        ];

        $contentType = $contentTypes[$ext] ?? 'application/octet-stream';

        header("Content-Type: $contentType");
        header("Content-Length: " . filesize($filepath));
        header("Cache-Control: public, max-age=3600");

        readfile($filepath);
        exit;
    }

    public function downloadFile($token, $filename)
    {
        RateLimitMiddleware::checkDownload();

        $delivery = $this->deliveryModel->findByToken($token);

        if (!$delivery || $this->deliveryModel->isExpired($delivery)) {
            Response::notFound();
        }

        $asset = $this->assetModel->findByDeliveryAndFilename($delivery['id'], $filename);

        if (!$asset) {
            Response::notFound();
        }

        // Log download
        $this->logger->logFileDownload(
            $delivery['id'],
            $asset['original_filename'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // Increment download count
        $this->deliveryModel->incrementDownloadCount($delivery['id']);

        // Send notification email to admin
        $mailer = new Mailer();
        $mailer->sendDownloadNotification(
            $this->config['studio_email'],
            $delivery['client_name'],
            $delivery['project_name'],
            $asset['original_filename']
        );

        // Serve file
        Response::download($asset['file_path'], $asset['original_filename']);
    }

    public function downloadAll($token)
    {
        RateLimitMiddleware::checkDownload();

        $delivery = $this->deliveryModel->findByToken($token);

        if (!$delivery || $this->deliveryModel->isExpired($delivery)) {
            Response::notFound();
        }

        $assets = $this->assetModel->findByDeliveryId($delivery['id']);

        if (empty($assets)) {
            Response::error('No files available');
        }

        // Create or get ZIP
        $zipService = new \Services\ZipService();
        $zipPath = $zipService->createDeliveryZip($delivery['id'], $assets);

        if (!$zipPath) {
            Response::error('Failed to create ZIP file');
        }

        // Log download
        $this->logger->logZipDownload(
            $delivery['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // Increment download count
        $this->deliveryModel->incrementDownloadCount($delivery['id']);

        // Send notification email to admin
        $mailer = new Mailer();
        $mailer->sendDownloadNotification(
            $this->config['studio_email'],
            $delivery['client_name'],
            $delivery['project_name']
        );

        // Serve ZIP
        $zipFilename = $delivery['project_name'] . '-logo-package.zip';
        Response::download($zipPath, $zipFilename);
    }

    public function requestTweak($token)
    {
        $delivery = $this->deliveryModel->findByToken($token);

        if (!$delivery || $this->deliveryModel->isExpired($delivery)) {
            Response::error('Invalid or expired delivery');
        }

        $message = $_POST['message'] ?? '';

        if (empty(trim($message))) {
            Response::error('Please provide a message');
        }

        // Log tweak request
        $this->logger->logTweakRequest(
            $delivery['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // Send email to admin
        $mailer = new Mailer();
        $mailer->sendTweakRequest(
            $this->config['studio_email'],
            $delivery['client_name'],
            $delivery['project_name'],
            htmlspecialchars($message)
        );

        Response::success('Thanks! I'll respond shortly.');
    }

    private function showExpired()
    {
        http_response_code(410);
        require __DIR__ . '/../Views/public/expired.php';
        exit;
    }

    private function showInvalid()
    {
        http_response_code(404);
        require __DIR__ . '/../Views/public/invalid.php';
        exit;
    }

    private function showPaused()
    {
        http_response_code(503);
        require __DIR__ . '/../Views/public/paused.php';
        exit;
    }
}
