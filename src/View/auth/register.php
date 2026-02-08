<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../layout/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">Регистрация</h2>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/register" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Логин</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= htmlspecialchars($formData['username'] ?? '') ?>" required
                                       pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="30">
                                <div class="invalid-feedback">
                                    Логин должен содержать от 3 до 30 символов и может включать только буквы, цифры и знак подчеркивания
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Введите корректный email адрес
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="display_name" class="form-label">Имя пользователя</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" 
                                       value="<?= htmlspecialchars($formData['display_name'] ?? '') ?>" required
                                       minlength="2" maxlength="30">
                                <div class="invalid-feedback">
                                    Имя пользователя должно содержать от 2 до 30 символов
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           pattern="^[a-zA-Z0-9!@#$%^&?*_]+$" required
                                           placeholder="Введите пароль">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        Пароль содержит недопустимые символы
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirm" class="form-label">Подтверждение пароля</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="password_confirm" 
                                           name="password_confirm" required
                                           placeholder="Подтвердите пароль">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        Пароли не совпадают
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Зарегистрироваться
                                </button>
                                <a href="/login" class="btn btn-outline-secondary">
                                    <i class="bi bi-box-arrow-in-right"></i> Уже есть аккаунт? Войти
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Валидация форм
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    // Проверка совпадения паролей
                    var password = document.getElementById('password')
                    var confirm = document.getElementById('password_confirm')
                    if (password.value !== confirm.value) {
                        confirm.setCustomValidity('Пароли не совпадают')
                    } else {
                        confirm.setCustomValidity('')
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Функция для переключения видимости пароля
        function togglePasswordVisibility(inputId, buttonId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            const icon = button.querySelector('i');

            button.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        }

        // Инициализация переключения видимости паролей
        togglePasswordVisibility('password', 'togglePassword');
        togglePasswordVisibility('password_confirm', 'togglePasswordConfirm');
    </script>
</body>
</html>
