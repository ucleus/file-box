<?php

namespace Controllers;

use Models\User;
use Models\Database;
use Services\TokenService;
use Services\Mailer;
use Middlewares\AuthMiddleware;
use Middlewares\RateLimitMiddleware;
use Utils\Response;
use Utils\Validator;

class AuthController
{
    private $userModel;
    private $db;
    private $config;

    public function __construct()
    {
        $this->userModel = new User();
        $this->db = Database::getInstance();
        $this->config = require __DIR__ . '/../../config/mail.php';
    }

    public function showLogin()
    {
        AuthMiddleware::guest();
        require __DIR__ . '/../Views/admin/login.php';
    }

    public function requestOTP()
    {
        RateLimitMiddleware::checkOTPRequest();

        $email = $_POST['email'] ?? '';

        if (!Validator::email($email)) {
            Response::error('Invalid email address');
        }

        // Get or create user
        $user = $this->userModel->findOrCreate($email);

        // Generate OTP
        $code = TokenService::generateOTP($this->config['otp_length']);
        $expiresAt = date('Y-m-d H:i:s', time() + ($this->config['otp_expires_minutes'] * 60));

        // Store OTP
        $sql = "INSERT INTO otp_codes (user_id, code, expires_at) VALUES (?, ?, ?)";
        $this->db->execute($sql, [$user['id'], $code, $expiresAt]);

        // Send email
        $mailer = new Mailer();
        $mailer->sendOTP($email, $code);

        Response::success('OTP sent to your email', ['email' => $email]);
    }

    public function verifyOTP()
    {
        RateLimitMiddleware::checkOTPVerify();

        $email = $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';

        if (!Validator::email($email) || !Validator::required($code)) {
            Response::error('Email and code are required');
        }

        // Find user
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            Response::error('Invalid credentials');
        }

        // Verify OTP
        $sql = "SELECT * FROM otp_codes
                WHERE user_id = ? AND code = ? AND used = 0 AND expires_at > datetime('now')
                ORDER BY created_at DESC LIMIT 1";

        $otp = $this->db->fetchOne($sql, [$user['id'], $code]);

        if (!$otp) {
            Response::error('Invalid or expired code');
        }

        // Mark OTP as used
        $this->db->execute("UPDATE otp_codes SET used = 1 WHERE id = ?", [$otp['id']]);

        // Log user in
        AuthMiddleware::login($user['id']);

        Response::success('Logged in successfully');
    }

    public function logout()
    {
        AuthMiddleware::logout();
        Response::redirect('/admin/login');
    }
}
