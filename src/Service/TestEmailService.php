<?php

require_once __DIR__ . '/EmailServiceInterface.php';

class TestEmailService implements EmailServiceInterface {
    private $logPath;
    private $siteUrl;

    public function __construct(string $logPath, string $siteUrl) {
        $this->logPath = $logPath;
        $this->siteUrl = $siteUrl;
        if (!file_exists($logPath)) {
            mkdir($logPath, 0777, true);
        }
    }

    public function sendVerificationEmail(string $email, string $token): bool {
        $content = sprintf(
            "Test Email Verification\nTo: %s\nToken: %s\nLink: %s\n",
            $email,
            $token,
            $this->getVerificationLink($token)
        );
        return $this->logEmail('verification', $email, $content);
    }

    public function sendPasswordResetEmail(string $email, string $token): bool {
        $content = sprintf(
            "Test Password Reset\nTo: %s\nToken: %s\nLink: %s\n",
            $email,
            $token,
            $this->getPasswordResetLink($token)
        );
        return $this->logEmail('password_reset', $email, $content);
    }

    public function sendEmailChangeConfirmation(string $email, string $token): bool {
        $content = sprintf(
            "Test Email Change Confirmation\nTo: %s\nToken: %s\nLink: %s\n",
            $email,
            $token,
            $this->getEmailChangeLink($token)
        );
        return $this->logEmail('email_change', $email, $content);
    }

    private function logEmail(string $type, string $email, string $content): bool {
        $filename = sprintf(
            '%s/%s_%s_%s.txt',
            $this->logPath,
            $type,
            $email,
            date('Y-m-d_H-i-s')
        );
        return file_put_contents($filename, $content) !== false;
    }

    private function getVerificationLink($token) {
        return "{$this->siteUrl}/verify-email/{$token}";
    }

    private function getPasswordResetLink($token) {
        return "{$this->siteUrl}/reset-password/{$token}";
    }

    private function getEmailChangeLink($token) {
        return "{$this->siteUrl}/confirm-email-change/{$token}";
    }
} 