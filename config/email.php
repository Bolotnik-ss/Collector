<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

return [
    'mode' => 'test', // 'test' или 'production'
    'test' => [
        'log_path' => __DIR__ . '/../logs/emails',
        'site_url' => 'http://localhost:8000' // URL для тестового окружения
    ],
    'production' => [
        'smtp_host' => 'smtp.example.com',
        'smtp_secure' => PHPMailer::ENCRYPTION_SMTPS,
        'smtp_port' => 465,
        'smtp_username' => 'user@example.com',
        'smtp_password' => 'your_password_here',
        'from_email' => 'noreply@example.com',
        'from_name' => 'Collection App',
        'site_url' => 'http://collection-app.local' // URL для производственного окружения
    ]
];
