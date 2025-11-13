<?php

namespace Models;

class Delivery
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByToken($token)
    {
        $sql = "SELECT * FROM deliveries WHERE token = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$token]);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM deliveries WHERE id = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function getAll($limit = 100, $offset = 0)
    {
        $sql = "SELECT * FROM deliveries ORDER BY created_at DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }

    public function getActiveCount()
    {
        $sql = "SELECT COUNT(*) as count FROM deliveries WHERE status = 'active'";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }

    public function getExpiringSoon($days = 7)
    {
        $sql = "SELECT * FROM deliveries
                WHERE status = 'active'
                AND expires_at IS NOT NULL
                AND expires_at <= datetime('now', '+{$days} days')
                ORDER BY expires_at ASC";
        return $this->db->fetchAll($sql);
    }

    public function create($data)
    {
        $sql = "INSERT INTO deliveries
                (token, client_name, client_email, project_name, project_version,
                 notes, brand_notes, passphrase, expires_at, max_downloads, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['token'],
            $data['client_name'],
            $data['client_email'] ?? null,
            $data['project_name'],
            $data['project_version'] ?? null,
            $data['notes'] ?? null,
            $data['brand_notes'] ?? null,
            $data['passphrase'] ?? null,
            $data['expires_at'] ?? null,
            $data['max_downloads'] ?? null,
            $data['status'] ?? 'active'
        ];

        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];

        $allowedFields = ['client_name', 'client_email', 'project_name', 'project_version',
                         'notes', 'brand_notes', 'status', 'passphrase', 'expires_at', 'max_downloads'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;

        $sql = "UPDATE deliveries SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->execute($sql, $params);
    }

    public function incrementDownloadCount($id)
    {
        $sql = "UPDATE deliveries SET download_count = download_count + 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM deliveries WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE deliveries SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }

    public function regenerateToken($id, $newToken)
    {
        $sql = "UPDATE deliveries SET token = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->execute($sql, [$newToken, $id]);
    }

    public function isExpired($delivery)
    {
        if ($delivery['status'] !== 'active') {
            return true;
        }

        // Check date expiry
        if ($delivery['expires_at'] && strtotime($delivery['expires_at']) < time()) {
            return true;
        }

        // Check download count limit
        if ($delivery['max_downloads'] && $delivery['download_count'] >= $delivery['max_downloads']) {
            return true;
        }

        return false;
    }

    public function isPaused($delivery)
    {
        return $delivery['status'] === 'paused';
    }
}
