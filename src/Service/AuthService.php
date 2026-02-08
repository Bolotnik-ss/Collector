<?php

require_once __DIR__ . '/EmailServiceFactory.php';

class AuthService {
    private $emailService;

    public function __construct() {
        $this->emailService = EmailServiceFactory::create();
    }

    public function register($username, $email, $password, $display_name) {
        // Проверяем валидность данных
        if (!User::validateUsername($username)) {
            throw new Exception('Имя пользователя может содержать только буквы, цифры и знак подчеркивания');
        }
        if (!User::validateEmail($email)) {
            throw new Exception('Некорректный email адрес');
        }
        if (!User::validatePassword($password)) {
            throw new Exception('Пароль может содержать только буквы, цифры и специальные символы !@#$%^&?*_');
        }

        // Проверяем, не занят ли username или email
        if (User::findByUsername($username)) {
            throw new Exception('Это логин уже зарегистрирован');
        }
        if (User::findByEmail($email)) {
            throw new Exception('Этот email уже зарегистрирован');
        }

        // Хешируем пароль
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Создаем пользователя
        $userId = User::create($username, $email, $hashedPassword, $display_name);
        if (!$userId) {
            throw new Exception('Ошибка при создании пользователя');
        }

        // Генерируем токен для подтверждения email
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Сохраняем токен в базе данных
        if (!User::setVerificationToken($userId, $token, $expires)) {
            throw new Exception('Ошибка при сохранении токена верификации');
        }

        // Отправляем письмо с подтверждением
        $emailService = EmailServiceFactory::create();
        $emailService->sendVerificationEmail($email, $token);

        return $userId;
    }

    public function verifyEmail($token) {
        $user = User::findByVerificationToken($token);
        if (!$user) {
            throw new Exception('Недействительная или просроченная ссылка подтверждения');
        }

        // Проверяем, не подтвержден ли уже email
        if ($user['email_verified']) {
            throw new Exception('Email уже подтвержден');
        }

        // Обновляем статус верификации
        if (!User::verifyEmail($user['id'])) {
            throw new Exception('Ошибка при подтверждении email');
        }

        return true;
    }

    public function requestPasswordReset($login) {
        // Определяем, является ли введенное значение email или логином
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        
        // Ищем пользователя по email или логину
        $user = $isEmail ? User::findByEmail($login) : User::findByUsername($login);
        
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        if (!User::setPasswordResetToken($user['id'], $token, $expires)) {
            throw new Exception('Ошибка при создании токена сброса пароля');
        }

        if (!$this->emailService->sendPasswordResetEmail($user['email'], $token)) {
            throw new Exception('Ошибка при отправке письма для сброса пароля');
        }

        return $user['email'];
    }

    public function resetPassword($token, $newPassword) {
        $user = User::findByPasswordResetToken($token);
        if (!$user) {
            throw new Exception('Недействительная или просроченная ссылка сброса пароля');
        }

        // Проверка валидности пароля
        if (!User::validatePassword($newPassword)) {
            throw new Exception('Пароль содержит недопустимые символы');
        }

        if (strlen($newPassword) < 6) {
            throw new Exception('Пароль должен содержать минимум 6 символов');
        }

        // Проверка, что новый пароль отличается от текущего
        if (password_verify($newPassword, $user['password'])) {
            throw new Exception('Новый пароль должен отличаться от текущего');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if (!User::updatePassword($user['id'], $hashedPassword)) {
            throw new Exception('Ошибка при обновлении пароля');
        }
        
        if (!User::clearPasswordResetToken($user['id'])) {
            throw new Exception('Ошибка при очистке токена сброса пароля');
        }

        return $user;
    }

    public function requestEmailChange($userId, $newEmail) {
        $user = User::findById($userId);
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        if ($newEmail === $user['email']) {
            throw new Exception('Новый email совпадает с текущим');
        }

        if (User::findByEmail($newEmail)) {
            throw new Exception('Этот email уже зарегистрирован');
        }

        if (!User::validateEmail($newEmail)) {
            throw new Exception('Некорректный формат email');
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        User::setEmailChangeToken($userId, $newEmail, $token, $expires);
        $this->emailService->sendEmailChangeConfirmation($newEmail, $token);
    }

    public function confirmEmailChange($token) {
        $user = User::findByEmailChangeToken($token);
        if (!$user) {
            throw new Exception('Недействительная или просроченная ссылка подтверждения');
        }

        return User::confirmEmailChange($user['id']);
    }

    public function login($login, $password, $autoLogin = false) {
        // Определяем, является ли введенное значение email или логином
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        
        // Ищем пользователя по email или логину
        $user = $isEmail ? User::findByEmail($login) : User::findByUsername($login);
        
        // Если это не автоматический вход, проверяем пароль
        if (!$autoLogin && (!$user || !password_verify($password, $user['password']))) {
            throw new Exception('Неверный логин/email или пароль');
        }

        // Проверяем верификацию email
        if (!$user['email_verified']) {
            // Если email не верифицирован, отправляем новое письмо
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            User::setVerificationToken($user['id'], $token, $expires);
            $this->emailService->sendVerificationEmail($user['email'], $token);
            
            throw new Exception('Пожалуйста, подтвердите ваш email. Новое письмо с подтверждением отправлено на указанную при регистрации почту.');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['display_name'] = $user['display_name'];
        $_SESSION['avatar'] = $user['avatar'] ?? '/images/default-avatar.png';
    }

    public function logout() {
        // Завершаем сессию пользователя
        session_unset();
        session_destroy();
    }

    public function updateProfile($userId, $data) {
        // Проверка наличия пользователя
        $user = User::findById($userId);
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        // Проверка уникальности логина, если он изменяется
        if (isset($data['username']) && $data['username'] !== $user['username']) {
            if (User::findByUsername($data['username'])) {
                throw new Exception('Этот логин уже занят');
            }
            if (!User::validateUsername($data['username'])) {
                throw new Exception('Логин может содержать только буквы, цифры и знак подчеркивания');
            }
        }

        // Валидация данных
        if (isset($data['display_name']) && (strlen($data['display_name']) < 2 || strlen($data['display_name']) > 30)) {
            throw new Exception('Имя пользователя должно содержать от 2 до 30 символов');
        }

        if (isset($data['birth_date']) && !$this->validateDate($data['birth_date'])) {
            throw new Exception('Некорректная дата рождения');
        }

        // Обновление профиля
        if (!User::update($userId, $data)) {
            throw new Exception('Ошибка при обновлении профиля');
        }

        // Обновляем данные в сессии
        if (isset($data['username'])) {
            $_SESSION['username'] = $data['username'];
        }
        if (isset($data['display_name'])) {
            $_SESSION['display_name'] = $data['display_name'];
        }
        if (isset($data['avatar'])) {
            $_SESSION['avatar'] = $data['avatar'];
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        // Проверка наличия пользователя
        $user = User::findById($userId);
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        // Проверка текущего пароля
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception('Неверный текущий пароль');
        }

        // Проверка совпадения паролей
        if ($newPassword !== $confirmPassword) {
            throw new Exception('Пароли не совпадают');
        }

        // Проверка, что новый пароль отличается от текущего
        if (password_verify($newPassword, $user['password'])) {
            throw new Exception('Новый пароль должен отличаться от текущего');
        }

        // Валидация нового пароля
        if (!User::validatePassword($newPassword)) {
            throw new Exception('Пароль содержит недопустимые символы');
        }

        if (strlen($newPassword) < 6) {
            throw new Exception('Пароль должен содержать минимум 6 символов');
        }

        // Хеширование нового пароля
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Обновление пароля
        if (!User::updatePassword($userId, $hashedPassword)) {
            throw new Exception('Ошибка при обновлении пароля');
        }
    }

    public function deleteAccount($userId, $confirmation) {
        // Проверка наличия пользователя
        $user = User::findById($userId);
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        // Проверка подтверждения
        if ($confirmation !== 'УДАЛИТЬ') {
            throw new Exception('Неверное подтверждение удаления');
        }

        // Удаляем аккаунт
        if (!User::delete($userId)) {
            throw new Exception('Ошибка при удалении аккаунта');
        }

        // Завершаем сессию
        $this->logout();
    }

    private function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
