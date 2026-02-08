<?php

interface EmailServiceInterface {
    /**
     * Отправляет письмо для подтверждения email при регистрации
     */
    public function sendVerificationEmail(string $email, string $token): bool;

    /**
     * Отправляет письмо для сброса пароля
     */
    public function sendPasswordResetEmail(string $email, string $token): bool;

    /**
     * Отправляет письмо для подтверждения смены email
     */
    public function sendEmailChangeConfirmation(string $email, string $token): bool;
} 