<?php

namespace Services;

use Models\Database;

class RateLimiter
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $securityConfig = require __DIR__ . '/../../config/security.php';
        $this->config = $securityConfig['rate_limits'];
    }

    public function check($identifier, $action)
    {
        if (!isset($this->config[$action])) {
            return true; // No limit configured
        }

        $limit = $this->config[$action];
        $maxAttempts = $limit['max_attempts'];
        $windowMinutes = $limit['window_minutes'];

        // Clean up old entries
        $this->cleanup($identifier, $action, $windowMinutes);

        // Check if blocked
        $blocked = $this->isBlocked($identifier, $action);
        if ($blocked) {
            return false;
        }

        // Get current attempts in window
        $attempts = $this->getAttempts($identifier, $action, $windowMinutes);

        if ($attempts >= $maxAttempts) {
            $this->block($identifier, $action, $windowMinutes);
            return false;
        }

        return true;
    }

    public function increment($identifier, $action)
    {
        $sql = "INSERT INTO rate_limits (identifier, action, attempts, window_start)
                VALUES (?, ?, 1, CURRENT_TIMESTAMP)
                ON CONFLICT(id) DO UPDATE SET attempts = attempts + 1";

        // For SQLite, we need to handle this differently
        $existing = $this->getCurrentRecord($identifier, $action);

        if ($existing) {
            $sql = "UPDATE rate_limits SET attempts = attempts + 1 WHERE identifier = ? AND action = ?";
            $this->db->execute($sql, [$identifier, $action]);
        } else {
            $sql = "INSERT INTO rate_limits (identifier, action, attempts) VALUES (?, ?, 1)";
            $this->db->execute($sql, [$identifier, $action]);
        }
    }

    private function getCurrentRecord($identifier, $action)
    {
        $sql = "SELECT * FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND window_start >= datetime('now', '-60 minutes')
                LIMIT 1";
        return $this->db->fetchOne($sql, [$identifier, $action]);
    }

    private function getAttempts($identifier, $action, $windowMinutes)
    {
        $sql = "SELECT SUM(attempts) as total
                FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND window_start >= datetime('now', '-{$windowMinutes} minutes')";

        $result = $this->db->fetchOne($sql, [$identifier, $action]);
        return $result['total'] ?? 0;
    }

    private function isBlocked($identifier, $action)
    {
        $sql = "SELECT COUNT(*) as count
                FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND blocked_until IS NOT NULL
                AND blocked_until > CURRENT_TIMESTAMP";

        $result = $this->db->fetchOne($sql, [$identifier, $action]);
        return ($result['count'] ?? 0) > 0;
    }

    private function block($identifier, $action, $minutes)
    {
        $sql = "UPDATE rate_limits
                SET blocked_until = datetime('now', '+{$minutes} minutes')
                WHERE identifier = ? AND action = ?";

        $this->db->execute($sql, [$identifier, $action]);
    }

    private function cleanup($identifier, $action, $windowMinutes)
    {
        $sql = "DELETE FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND window_start < datetime('now', '-{$windowMinutes} minutes')
                AND (blocked_until IS NULL OR blocked_until < CURRENT_TIMESTAMP)";

        $this->db->execute($sql, [$identifier, $action]);
    }

    public function reset($identifier, $action)
    {
        $sql = "DELETE FROM rate_limits WHERE identifier = ? AND action = ?";
        return $this->db->execute($sql, [$identifier, $action]);
    }

    public static function getIdentifier()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return hash('sha256', $ip);
    }
}
