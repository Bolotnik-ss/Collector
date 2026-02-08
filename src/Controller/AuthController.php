<?php

class AuthController {

    private function maskEmail($email) {
        $parts = explode('@', $email);
        $username = $parts[0];
        $domain = $parts[1];
        
        // Маскируем имя пользователя, оставляя первые 2 и последние 2 символа
        $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 4) . substr($username, -2);
        
        // Маскируем домен, оставляя только первые 2 символа
        $maskedDomain = substr($domain, 0, 2) . str_repeat('*', strlen($domain) - 2);
        
        return $maskedUsername . '@' . $maskedDomain;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Получаем данные из формы
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $display_name = $_POST['display_name'];

            // Проверяем совпадение паролей
            if ($password !== $password_confirm) {
                $error = "Пароли не совпадают";
                $formData = [
                    'username' => $username,
                    'email' => $email,
                    'display_name' => $display_name
                ];
                require __DIR__ . '/../View/auth/register.php';
                return;
            }

            try {
                $authService = new AuthService();
                $authService->register($username, $email, $password, $display_name);

                // Маскируем email для отображения
                $maskedEmail = $this->maskEmail($email);

                // Показываем страницу успешной регистрации
                require __DIR__ . '/../View/auth/registration_success.php';
            } catch (Exception $e) {
                $error = $e->getMessage();
                $formData = [
                    'username' => $username,
                    'email' => $email,
                    'display_name' => $display_name
                ];
                require __DIR__ . '/../View/auth/register.php';
            }
        } else {
            require __DIR__ . '/../View/auth/register.php';
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Получаем данные из формы
            $login = $_POST['login'];
            $password = $_POST['password'];

            try {
                $authService = new AuthService();
                $authService->login($login, $password);

                header('Location: /');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
                $formData = [
                    'login' => $login
                ];
                require __DIR__ . '/../View/auth/login.php';
            }
        } else {
            require __DIR__ . '/../View/auth/login.php';
        }
    }

    public function logout() {
        $authService = new AuthService();
        $authService->logout();
        header('Location: /login');
        exit();
    }

    public function profile() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $authService = new AuthService();
                $data = [
                    'username' => $_POST['username'],
                    'display_name' => $_POST['display_name'],
                    'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
                    'gender' => $_POST['gender'] !== '' ? (int)$_POST['gender'] : null,
                    'info' => !empty($_POST['info']) ? $_POST['info'] : null
                ];

                // Обработка удаления аватара
                if (isset($_POST['delete_avatar']) && $_POST['delete_avatar'] === '1') {
                    $data['avatar'] = null;
                    $_SESSION['avatar'] = '/images/default-avatar.png';
                }
                // Обработка загрузки аватара
                else if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../../public/uploads/avatars/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                    if (!in_array($file_extension, $allowed_extensions)) {
                        throw new Exception('Недопустимый формат файла. Разрешены только: ' . implode(', ', $allowed_extensions));
                    }

                    $file_name = uniqid() . '.' . $file_extension;
                    $file_path = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
                        $data['avatar'] = '/uploads/avatars/' . $file_name;
                        $_SESSION['avatar'] = $data['avatar'];
                    }
                }

                $authService->updateProfile($_SESSION['user_id'], $data);
                header('Location: /profile');
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
                $user = $data; // Используем введенные данные при ошибке
                // Сохраняем текущий аватар при ошибке
                $user['avatar'] = $_SESSION['avatar'] ?? '/images/default-avatar.png';
                require __DIR__ . '/../View/auth/profile.php';
            }
        } else {
            $user = User::findById($_SESSION['user_id']);
            require __DIR__ . '/../View/auth/profile.php';
        }
    }

    public function updateProfile() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit();
        }

        try {
            $authService = new AuthService();
            $data = [
                'display_name' => $_POST['display_name'],
                'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
                'gender' => !empty($_POST['gender']) ? $_POST['gender'] : null,
                'info' => !empty($_POST['info']) ? $_POST['info'] : null
            ];

            // Обработка загрузки аватара
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../public/uploads/avatars/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($file_extension, $allowed_extensions)) {
                    throw new Exception('Недопустимый формат файла. Разрешены только: ' . implode(', ', $allowed_extensions));
                }

                $file_name = uniqid() . '.' . $file_extension;
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
                    $data['avatar'] = '/uploads/avatars/' . $file_name;
                }
            }

            $authService->updateProfile($_SESSION['user_id'], $data);
            header('Location: /profile');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /profile');
            exit();
        }
    }

    public function changePassword() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $authService = new AuthService();
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                $authService->changePassword(
                    $_SESSION['user_id'],
                    $current_password,
                    $new_password,
                    $confirm_password
                );

                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit();
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
        } else {
            header('Location: /profile');
            exit();
        }
    }

    public function changeEmail() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $authService = new AuthService();
                $new_email = $_POST['new_email'];

                $authService->requestEmailChange($_SESSION['user_id'], $new_email);

                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit();
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
        } else {
            header('Location: /profile');
            exit();
        }
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $login = $_POST['login'];
                $authService = new AuthService();
                $email = $authService->requestPasswordReset($login);
                
                // Маскируем email для отображения
                $maskedEmail = $this->maskEmail($email);
                
                require __DIR__ . '/../View/auth/reset_password_sent.php';
            } catch (Exception $e) {
                $error = $e->getMessage();
                $formData = [
                    'login' => $login
                ];
                require __DIR__ . '/../View/auth/reset_password.php';
            }
        } else {
            require __DIR__ . '/../View/auth/reset_password.php';
        }
    }

    public function verifyEmail($token) {
        try {
            // Сначала получаем пользователя по токену
            $user = User::findByVerificationToken($token);
            if (!$user) {
                $isVerified = false;
            } else {
                $authService = new AuthService();
                $isVerified = $authService->verifyEmail($token);
                
                if ($isVerified) {
                    // Выполняем автоматический вход через AuthService
                    $authService->login($user['email'], null, true);
                }
            }
            
            // Всегда показываем страницу подтверждения, но с разным контентом
            require __DIR__ . '/../View/auth/email_verified.php';
        } catch (Exception $e) {
            // В случае ошибки тоже показываем страницу подтверждения
            require __DIR__ . '/../View/auth/email_verified.php';
        }
    }

    public function resetPasswordConfirm($token) {
        try {
            // Проверяем валидность токена
            $user = User::findByPasswordResetToken($token);
            if (!$user) {
                $isSuccess = false;
                require __DIR__ . '/../View/auth/reset_password_verified.php';
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $newPassword = $_POST['new_password'];
                    $confirmPassword = $_POST['confirm_password'];

                    if ($newPassword !== $confirmPassword) {
                        throw new Exception('Пароли не совпадают');
                    }

                    $authService = new AuthService();
                    $user = $authService->resetPassword($token, $newPassword);
                    
                    // Выполняем автоматический вход
                    $authService->login($user['email'], $newPassword);
                    
                    $isSuccess = true;
                    require __DIR__ . '/../View/auth/reset_password_verified.php';
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    require __DIR__ . '/../View/auth/reset_password_confirm.php';
                }
            } else {
                require __DIR__ . '/../View/auth/reset_password_confirm.php';
            }
        } catch (Exception $e) {
            $isSuccess = false;
            require __DIR__ . '/../View/auth/reset_password_verified.php';
        }
    }

    public function confirmEmailChange($token) {
        try {
            $authService = new AuthService();
            $isSuccess = $authService->confirmEmailChange($token);
            
            // Показываем страницу подтверждения
            require __DIR__ . '/../View/auth/email_change_verified.php';
        } catch (Exception $e) {
            $isSuccess = false;
            require __DIR__ . '/../View/auth/email_change_verified.php';
        }
    }

    public function deleteAccount() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $authService = new AuthService();
                $confirmation = $_POST['confirmation'] ?? '';
                
                $authService->deleteAccount($_SESSION['user_id'], $confirmation);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit();
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
        } else {
            header('Location: /profile');
            exit();
        }
    }
}
