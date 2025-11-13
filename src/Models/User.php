<?php

namespace Models;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$email]);
    }

    public function create($email)
    {
        $sql = "INSERT INTO users (email) VALUES (?)";
        $this->db->execute($sql, [$email]);
        return $this->db->lastInsertId();
    }

    public function findOrCreate($email)
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            $id = $this->create($email);
            $user = ['id' => $id, 'email' => $email];
        }
        return $user;
    }
}
