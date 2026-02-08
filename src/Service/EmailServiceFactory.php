<?php

require_once __DIR__ . '/EmailServiceInterface.php';
require_once __DIR__ . '/TestEmailService.php';
require_once __DIR__ . '/SmtpEmailService.php';

class EmailServiceFactory {
    public static function create(): EmailServiceInterface {
        $config = require __DIR__ . '/../../config/email.php';
        
        if ($config['mode'] === 'test') {
            return new TestEmailService(
                $config['test']['log_path'],
                $config['test']['site_url']
            );
        }
        
        if ($config['mode'] === 'production') {
            return new SmtpEmailService($config['production']);
        }
        
        throw new Exception('Invalid email service mode: ' . $config['mode']);
    }
} 