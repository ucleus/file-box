<?php

namespace Services;

class Mailer
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
    }

    public function sendOTP($email, $code)
    {
        $subject = 'Your sign-in code';
        $body = "Your sign-in code: <strong>{$code}</strong><br><br>This code expires in {$this->config['otp_expires_minutes']} minutes.";

        return $this->send($email, $subject, $body);
    }

    public function sendDeliveryLink($email, $clientName, $projectName, $url, $notes = '', $expiresAt = null)
    {
        $subject = "Your logo package is ready — {$projectName}";

        $expiryText = $expiresAt
            ? "This link expires on " . date('F j, Y', strtotime($expiresAt)) . "."
            : "This link has no expiry.";

        $notesSection = $notes ? "<p><strong>Notes:</strong> {$notes}</p>" : '';

        $body = "
            <p>Hey {$clientName},</p>
            <p>Your logo package for <strong>{$projectName}</strong> is ready.</p>
            <p><a href='{$url}' style='display:inline-block;padding:12px 24px;background:#450693;color:white;text-decoration:none;border-radius:6px;'>Download Your Files</a></p>
            {$notesSection}
            <p>{$expiryText}</p>
            <p>If you need tweaks, hit 'Request a Tweak' on the page.</p>
            <p>— Ucleus</p>
        ";

        return $this->send($email, $subject, $body);
    }

    public function sendDownloadNotification($adminEmail, $clientName, $projectName, $fileName = null)
    {
        $subject = "Download notification — {$projectName}";

        $fileText = $fileName ? " downloaded <strong>{$fileName}</strong>" : " downloaded the complete package";

        $body = "
            <p>{$clientName}{$fileText} from the {$projectName} delivery.</p>
            <p>Time: " . date('F j, Y g:i A') . "</p>
        ";

        return $this->send($adminEmail, $subject, $body);
    }

    public function sendTweakRequest($adminEmail, $clientName, $projectName, $message)
    {
        $subject = "Tweak request — {$projectName}";

        $body = "
            <p>{$clientName} has requested a tweak for <strong>{$projectName}</strong>:</p>
            <blockquote style='border-left:3px solid #450693;padding-left:15px;margin:15px 0;color:#666;'>
                {$message}
            </blockquote>
        ";

        return $this->send($adminEmail, $subject, $body);
    }

    private function send($to, $subject, $body)
    {
        $headers = [
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=utf-8',
            'From' => "{$this->config['from_name']} <{$this->config['from_email']}>",
            'Reply-To' => $this->config['from_email'],
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        $headerString = '';
        foreach ($headers as $key => $value) {
            $headerString .= "$key: $value\r\n";
        }

        // For production with SMTP, you would use PHPMailer or similar
        // For now, using basic mail() function
        if ($this->useSMTP()) {
            return $this->sendViaSMTP($to, $subject, $body);
        }

        return mail($to, $subject, $body, $headerString);
    }

    private function useSMTP()
    {
        return !empty($this->config['smtp_host']) && !empty($this->config['smtp_username']);
    }

    private function sendViaSMTP($to, $subject, $body)
    {
        // Basic SMTP implementation using fsockopen
        // In production, consider using PHPMailer or SwiftMailer

        $from = $this->config['from_email'];
        $host = $this->config['smtp_host'];
        $port = $this->config['smtp_port'];
        $username = $this->config['smtp_username'];
        $password = $this->config['smtp_password'];

        try {
            $socket = fsockopen($host, $port, $errno, $errstr, 30);
            if (!$socket) {
                error_log("SMTP connection failed: $errstr ($errno)");
                return false;
            }

            // Simple SMTP conversation
            $this->smtpCommand($socket, "EHLO {$host}\r\n");
            $this->smtpCommand($socket, "AUTH LOGIN\r\n");
            $this->smtpCommand($socket, base64_encode($username) . "\r\n");
            $this->smtpCommand($socket, base64_encode($password) . "\r\n");
            $this->smtpCommand($socket, "MAIL FROM: <{$from}>\r\n");
            $this->smtpCommand($socket, "RCPT TO: <{$to}>\r\n");
            $this->smtpCommand($socket, "DATA\r\n");

            $message = "Subject: {$subject}\r\n";
            $message .= "From: {$from}\r\n";
            $message .= "To: {$to}\r\n";
            $message .= "Content-Type: text/html; charset=utf-8\r\n";
            $message .= "\r\n{$body}\r\n.\r\n";

            fwrite($socket, $message);
            fgets($socket, 512);

            $this->smtpCommand($socket, "QUIT\r\n");
            fclose($socket);

            return true;
        } catch (\Exception $e) {
            error_log("SMTP error: " . $e->getMessage());
            return false;
        }
    }

    private function smtpCommand($socket, $command)
    {
        fwrite($socket, $command);
        return fgets($socket, 512);
    }
}
