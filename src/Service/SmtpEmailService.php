<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/EmailServiceInterface.php';

class SmtpEmailService implements EmailServiceInterface {
    private $config;
    private $siteUrl;

    public function __construct(array $config) {
        $this->config = $config;
        $this->siteUrl = $config['site_url'];
    }

    public function sendVerificationEmail(string $email, string $token): bool {
        $subject = "Подтверждение регистрации";
        $message = $this->getEmailTemplate('verification', [
            'link' => $this->getVerificationLink($token)
        ]);
        return $this->sendEmail($email, $subject, $message);
    }

    public function sendPasswordResetEmail(string $email, string $token): bool {
        $subject = "Сброс пароля";
        $message = $this->getEmailTemplate('password_reset', [
            'link' => $this->getPasswordResetLink($token)
        ]);
        return $this->sendEmail($email, $subject, $message);
    }

    public function sendEmailChangeConfirmation(string $email, string $token): bool {
        $subject = "Подтверждение смены email";
        $message = $this->getEmailTemplate('email_change', [
            'link' => $this->getEmailChangeLink($token)
        ]);
        return $this->sendEmail($email, $subject, $message);
    }

    private function sendEmail(string $to, string $subject, string $message): bool {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_username'];
            $mail->Password = $this->config['smtp_password'];
            $mail->SMTPSecure = $this->config['smtp_secure'];
            $mail->Port = $this->config['smtp_port'];

            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);
            $mail->CharSet = 'UTF-8'; 
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email send error: " . $mail->ErrorInfo);
            return false;
        }
    }

    private function getEmailTemplate(string $type, array $data): string {
        $template = match($type) {
            'verification' => $this->getVerificationTemplate(),
            'password_reset' => $this->getPasswordResetTemplate(),
            'email_change' => $this->getEmailChangeTemplate(),
            default => throw new \Exception('Unknown email template type')
        };

        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", htmlspecialchars($value), $template);
        }

        return $template;
    }

    private function getVerificationTemplate(): string {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Подтверждение регистрации</title>
</head>
<body>
    <h2>Подтверждение регистрации</h2>
    <p>Спасибо за регистрацию! Для подтверждения вашего email адреса, пожалуйста, перейдите по следующей ссылке:</p>
    <p><a href="{link}">Подтвердить email</a></p>
    <p>Если вы не регистрировались на нашем сайте, просто проигнорируйте это письмо.</p>
    <p>Ссылка действительна в течение 24 часов.</p>
</body>
</html>
HTML;
    }

    private function getPasswordResetTemplate(): string {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Сброс пароля</title>
</head>
<body>
    <h2>Сброс пароля</h2>
    <p>Вы запросили сброс пароля. Для установки нового пароля, пожалуйста, перейдите по следующей ссылке:</p>
    <p><a href="{link}">Сбросить пароль</a></p>
    <p>Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>
    <p>Ссылка действительна в течение 24 часов.</p>
</body>
</html>
HTML;
    }

    private function getEmailChangeTemplate(): string {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Подтверждение смены email</title>
</head>
<body>
    <h2>Подтверждение смены email</h2>
    <p>Вы запросили изменение email адреса. Для подтверждения, пожалуйста, перейдите по следующей ссылке:</p>
    <p><a href="{link}">Подтвердить смену email</a></p>
    <p>Если вы не запрашивали смену email, просто проигнорируйте это письмо.</p>
    <p>Ссылка действительна в течение 24 часов.</p>
</body>
</html>
HTML;
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
