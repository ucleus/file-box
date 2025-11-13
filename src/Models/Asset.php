<?php

namespace Models;

class Asset
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM assets WHERE id = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function findByDeliveryId($deliveryId)
    {
        $sql = "SELECT * FROM assets WHERE delivery_id = ? ORDER BY sort_order ASC, created_at ASC";
        return $this->db->fetchAll($sql, [$deliveryId]);
    }

    public function findByDeliveryAndFilename($deliveryId, $filename)
    {
        $sql = "SELECT * FROM assets WHERE delivery_id = ? AND filename = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$deliveryId, $filename]);
    }

    public function create($data)
    {
        $sql = "INSERT INTO assets
                (delivery_id, filename, original_filename, file_path, file_size, file_type, asset_tag, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['delivery_id'],
            $data['filename'],
            $data['original_filename'],
            $data['file_path'],
            $data['file_size'],
            $data['file_type'],
            $data['asset_tag'] ?? null,
            $data['sort_order'] ?? 0
        ];

        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];

        $allowedFields = ['filename', 'original_filename', 'asset_tag', 'sort_order'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE assets SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->execute($sql, $params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM assets WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function deleteByDeliveryId($deliveryId)
    {
        $sql = "DELETE FROM assets WHERE delivery_id = ?";
        return $this->db->execute($sql, [$deliveryId]);
    }

    public function getTotalSize($deliveryId)
    {
        $sql = "SELECT SUM(file_size) as total FROM assets WHERE delivery_id = ?";
        $result = $this->db->fetchOne($sql, [$deliveryId]);
        return $result['total'] ?? 0;
    }

    public function getFileTypeStats()
    {
        $sql = "SELECT file_type, COUNT(*) as count, SUM(file_size) as total_size
                FROM assets
                GROUP BY file_type
                ORDER BY count DESC";
        return $this->db->fetchAll($sql);
    }
}
