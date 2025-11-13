<?php

namespace Services;

class ZipService
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/app.php';
    }

    public function createDeliveryZip($deliveryId, $assets)
    {
        if (empty($assets)) {
            return false;
        }

        $zipPath = $this->config['zips_path'] . '/' . $deliveryId . '.zip';

        // Create zips directory if it doesn't exist
        if (!is_dir($this->config['zips_path'])) {
            mkdir($this->config['zips_path'], 0755, true);
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        foreach ($assets as $asset) {
            if (file_exists($asset['file_path'])) {
                $zip->addFile($asset['file_path'], $asset['original_filename']);
            }
        }

        $zip->close();

        return file_exists($zipPath) ? $zipPath : false;
    }

    public function getZipPath($deliveryId)
    {
        return $this->config['zips_path'] . '/' . $deliveryId . '.zip';
    }

    public function zipExists($deliveryId)
    {
        $zipPath = $this->getZipPath($deliveryId);
        return file_exists($zipPath);
    }

    public function deleteZip($deliveryId)
    {
        $zipPath = $this->getZipPath($deliveryId);
        if (file_exists($zipPath)) {
            return unlink($zipPath);
        }
        return true;
    }

    public function getZipSize($deliveryId)
    {
        $zipPath = $this->getZipPath($deliveryId);
        if (file_exists($zipPath)) {
            return filesize($zipPath);
        }
        return 0;
    }
}
