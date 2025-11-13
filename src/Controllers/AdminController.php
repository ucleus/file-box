<?php

namespace Controllers;

use Models\Delivery;
use Models\Asset;
use Services\TokenService;
use Services\ZipService;
use Services\Logger;
use Services\Mailer;
use Middlewares\AuthMiddleware;
use Utils\Response;
use Utils\Validator;

class AdminController
{
    private $deliveryModel;
    private $assetModel;
    private $logger;
    private $config;

    public function __construct()
    {
        AuthMiddleware::check();
        $this->deliveryModel = new Delivery();
        $this->assetModel = new Asset();
        $this->logger = new Logger();
        $this->config = require __DIR__ . '/../../config/app.php';
    }

    public function dashboard()
    {
        $deliveries = $this->deliveryModel->getAll(50);
        $stats = $this->getStats();

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function showNewDelivery()
    {
        require __DIR__ . '/../Views/admin/new-delivery.php';
    }

    public function createDelivery()
    {
        $data = [
            'client_name' => $_POST['client_name'] ?? '',
            'client_email' => $_POST['client_email'] ?? '',
            'project_name' => $_POST['project_name'] ?? '',
            'project_version' => $_POST['project_version'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'brand_notes' => $_POST['brand_notes'] ?? '',
            'expires_at' => $_POST['expires_at'] ?? null,
            'max_downloads' => $_POST['max_downloads'] ?? null,
        ];

        // Validate
        $errors = Validator::validateDelivery($data);
        if (!empty($errors)) {
            Response::error(implode(', ', $errors));
        }

        // Generate token
        $data['token'] = TokenService::generate();
        $data['status'] = 'active';

        // Create delivery
        $deliveryId = $this->deliveryModel->create($data);

        if (!$deliveryId) {
            Response::error('Failed to create delivery');
        }

        Response::success('Delivery created successfully', [
            'delivery_id' => $deliveryId,
            'token' => $data['token']
        ]);
    }

    public function uploadFiles()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;

        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $delivery = $this->deliveryModel->findById($deliveryId);
        if (!$delivery) {
            Response::error('Delivery not found');
        }

        if (empty($_FILES['files'])) {
            Response::error('No files uploaded');
        }

        $files = $this->reArrayFiles($_FILES['files']);
        $uploadedFiles = [];
        $errors = [];

        // Create delivery directory
        $deliveryPath = $this->config['deliveries_path'] . '/' . $deliveryId . '/assets';
        if (!is_dir($deliveryPath)) {
            mkdir($deliveryPath, 0755, true);
        }

        foreach ($files as $index => $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Failed to upload {$file['name']}";
                continue;
            }

            // Validate file
            if (!Validator::fileExtension($file['name'], $this->config['allowed_extensions'])) {
                $errors[] = "{$file['name']}: Invalid file type";
                continue;
            }

            if (!Validator::fileSize($file['size'], $this->config['max_upload_size'])) {
                $errors[] = "{$file['name']}: File too large";
                continue;
            }

            // Generate unique filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $filepath = $deliveryPath . '/' . $filename;

            // Move file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Get asset tag if provided
                $assetTag = $_POST['asset_tags'][$index] ?? null;

                // Save to database
                $assetId = $this->assetModel->create([
                    'delivery_id' => $deliveryId,
                    'filename' => $filename,
                    'original_filename' => $file['name'],
                    'file_path' => $filepath,
                    'file_size' => $file['size'],
                    'file_type' => strtoupper($ext),
                    'asset_tag' => $assetTag,
                    'sort_order' => $index
                ]);

                $uploadedFiles[] = [
                    'id' => $assetId,
                    'name' => $file['name'],
                    'size' => $file['size'],
                    'type' => $ext
                ];
            } else {
                $errors[] = "Failed to save {$file['name']}";
            }
        }

        if (!empty($errors)) {
            Response::error(implode(', ', $errors));
        }

        Response::success('Files uploaded successfully', ['files' => $uploadedFiles]);
    }

    public function sendDeliveryEmail()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;

        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $delivery = $this->deliveryModel->findById($deliveryId);
        if (!$delivery) {
            Response::error('Delivery not found');
        }

        if (empty($delivery['client_email'])) {
            Response::error('No email address for this client');
        }

        $url = $this->config['base_url'] . '/dl/' . $delivery['token'];

        $mailer = new Mailer();
        $result = $mailer->sendDeliveryLink(
            $delivery['client_email'],
            $delivery['client_name'],
            $delivery['project_name'],
            $url,
            $delivery['notes'] ?? '',
            $delivery['expires_at']
        );

        if ($result) {
            Response::success('Email sent successfully');
        } else {
            Response::error('Failed to send email');
        }
    }

    public function pauseDelivery()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;
        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $this->deliveryModel->updateStatus($deliveryId, 'paused');
        Response::success('Delivery paused');
    }

    public function resumeDelivery()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;
        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $this->deliveryModel->updateStatus($deliveryId, 'active');
        Response::success('Delivery resumed');
    }

    public function expireDelivery()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;
        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $this->deliveryModel->updateStatus($deliveryId, 'expired');
        Response::success('Delivery expired');
    }

    public function regenerateToken()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;
        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $newToken = TokenService::generate();
        $this->deliveryModel->regenerateToken($deliveryId, $newToken);

        Response::success('Token regenerated', ['token' => $newToken]);
    }

    public function repackageZip()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;
        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $assets = $this->assetModel->findByDeliveryId($deliveryId);
        if (empty($assets)) {
            Response::error('No files to package');
        }

        $zipService = new ZipService();
        $zipPath = $zipService->createDeliveryZip($deliveryId, $assets);

        if ($zipPath) {
            Response::success('ZIP repackaged successfully');
        } else {
            Response::error('Failed to create ZIP');
        }
    }

    public function deleteDelivery()
    {
        $deliveryId = $_POST['delivery_id'] ?? null;
        if (!$deliveryId) {
            Response::error('Delivery ID is required');
        }

        $delivery = $this->deliveryModel->findById($deliveryId);
        if (!$delivery) {
            Response::error('Delivery not found');
        }

        // Delete files
        $deliveryPath = $this->config['deliveries_path'] . '/' . $deliveryId;
        if (is_dir($deliveryPath)) {
            $this->deleteDirectory($deliveryPath);
        }

        // Delete ZIP
        $zipService = new ZipService();
        $zipService->deleteZip($deliveryId);

        // Delete from database (cascade will handle assets and logs)
        $this->deliveryModel->delete($deliveryId);

        Response::success('Delivery deleted');
    }

    private function getStats()
    {
        return [
            'total_deliveries' => count($this->deliveryModel->getAll(9999)),
            'active_deliveries' => $this->deliveryModel->getActiveCount(),
            'expiring_soon' => count($this->deliveryModel->getExpiringSoon()),
            'file_type_stats' => $this->assetModel->getFileTypeStats(),
            'recent_activity' => $this->logger->getRecentActivity(10),
        ];
    }

    private function reArrayFiles($files)
    {
        $fileArray = [];
        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            $fileArray[] = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
        }

        return $fileArray;
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
