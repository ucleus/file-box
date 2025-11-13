<?php

namespace Services;

use Models\Database;

class Logger
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function log($deliveryId, $eventType, $fileName = null, $ipAddress = null, $userAgent = null)
    {
        $ipHash = $ipAddress ? hash('sha256', $ipAddress) : null;

        $sql = "INSERT INTO activity_log (delivery_id, event_type, file_name, ip_hash, user_agent)
                VALUES (?, ?, ?, ?, ?)";

        return $this->db->execute($sql, [$deliveryId, $eventType, $fileName, $ipHash, $userAgent]);
    }

    public function logPageView($deliveryId, $ipAddress = null, $userAgent = null)
    {
        return $this->log($deliveryId, 'page_view', null, $ipAddress, $userAgent);
    }

    public function logFileDownload($deliveryId, $fileName, $ipAddress = null, $userAgent = null)
    {
        return $this->log($deliveryId, 'file_download', $fileName, $ipAddress, $userAgent);
    }

    public function logZipDownload($deliveryId, $ipAddress = null, $userAgent = null)
    {
        return $this->log($deliveryId, 'zip_download', null, $ipAddress, $userAgent);
    }

    public function logTweakRequest($deliveryId, $ipAddress = null, $userAgent = null)
    {
        return $this->log($deliveryId, 'tweak_request', null, $ipAddress, $userAgent);
    }

    public function getDeliveryStats($deliveryId)
    {
        $sql = "SELECT event_type, COUNT(*) as count
                FROM activity_log
                WHERE delivery_id = ?
                GROUP BY event_type";

        return $this->db->fetchAll($sql, [$deliveryId]);
    }

    public function getTotalStats()
    {
        $sql = "SELECT event_type, COUNT(*) as count
                FROM activity_log
                GROUP BY event_type";

        return $this->db->fetchAll($sql);
    }

    public function getRecentActivity($limit = 50)
    {
        $sql = "SELECT al.*, d.project_name, d.client_name
                FROM activity_log al
                LEFT JOIN deliveries d ON al.delivery_id = d.id
                ORDER BY al.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    public function cleanOldLogs($days = 90)
    {
        $sql = "DELETE FROM activity_log WHERE created_at < datetime('now', '-{$days} days')";
        return $this->db->execute($sql);
    }
}
