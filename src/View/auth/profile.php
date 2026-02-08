<?php 
$page_title = 'Профиль';
include __DIR__ . '/../layout/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <form method="POST" action="/profile" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="text-center mb-4">
                            <div class="avatar-container">
                                <img src="<?= $user['avatar'] ?? '/images/default-avatar.png' ?>" 
                                     alt="Аватар" class="avatar-preview" id="avatarPreview">
                                <?php if ($user['avatar']): ?>
                                <button type="button" class="avatar-delete" id="deleteAvatar" title="Удалить аватар">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                <?php endif; ?>
                                <label for="avatar" class="avatar-upload" title="Изменить аватар">
                                    <i class="bi bi-paperclip"></i>
                                </label>
                                <input type="file" id="avatar" name="avatar" class="d-none" 
                                       accept="image/jpeg,image/png,image/gif">
                                <input type="hidden" name="delete_avatar" id="deleteAvatarInput" value="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Логин</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= htmlspecialchars($user['username']) ?>" required
                                       pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="30">
                                <div class="invalid-feedback">
                                    Логин должен содержать от 3 до 30 символов и может включать только буквы, цифры и знак подчеркивания
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="display_name" class="form-label">Имя пользователя</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" 
                                       value="<?= htmlspecialchars($user['display_name']) ?>" required
                                       minlength="2" maxlength="30">
                                <div class="invalid-feedback">
                                    Имя пользователя должно содержать от 2 до 30 символов
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Дата рождения</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                       value="<?= htmlspecialchars($user['birth_date'] ?? '') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Пол</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Не указан</option>
                                    <option value="1" <?= $user['gender'] === 1 ? 'selected' : '' ?>>Мужской</option>
                                    <option value="0" <?= $user['gender'] === 0 ? 'selected' : '' ?>>Женский</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="info" class="form-label">О себе</label>
                            <textarea class="form-control" id="info" name="info" rows="3"><?= htmlspecialchars($user['info'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Сохранить изменения
                            </button>
                        </div>
                    </form>

                    <div class="profile-info mt-3">
                        <div class="mb-3">
                            <label class="form-label text-muted">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changeEmailModal">
                                    <i class="bi bi-pencil"></i> Изменить
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-key"></i> Изменить пароль
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash"></i> Удалить аккаунт
                        </button>
                    </div>

                    <div class="profile-info mt-3">
                        <p><strong>Дата регистрации:</strong> <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно изменения пароля -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Изменение пароля</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/change_password" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="alert alert-danger mt-3 d-none" id="passwordErrorAlert"></div>
                    <div class="alert alert-success mt-3 d-none" id="passwordSuccessAlert"></div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Текущий пароль</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Введите текущий пароль
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Новый пароль</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   required minlength="6" pattern="[a-zA-Z0-9!@#$%^&?*_]+">
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Пароль должен содержать минимум 6 символов и может включать только буквы, цифры и специальные символы
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Подтверждение пароля</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Пароли не совпадают
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Изменить пароль</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно удаления аккаунта -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">Удаление аккаунта</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/delete_account" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h4 class="alert-heading">Внимание!</h4>
                        <p>Это действие нельзя отменить. Все ваши данные будут безвозвратно удалены.</p>
                        <hr>
                        <p class="mb-0">Для подтверждения введите слово "УДАЛИТЬ" в поле ниже:</p>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="deleteAccountErrorAlert"></div>

                    <div class="mb-3">
                        <label for="confirmation" class="form-label">Подтверждение</label>
                        <input type="text" class="form-control" id="confirmation" name="confirmation" 
                               required pattern="УДАЛИТЬ">
                        <div class="invalid-feedback">
                            Введите слово "УДАЛИТЬ" для подтверждения
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-danger">Удалить аккаунт</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно изменения email -->
<div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeEmailModalLabel">Изменение email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/change_email" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h4 class="alert-heading mb-3">Внимание!</h4>
                        <p class="mb-0">После изменения email вам потребуется подтвердить новый адрес электронной почты.</p>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="emailErrorAlert"></div>
                    <div class="alert alert-success mt-3 d-none" id="emailSuccessAlert"></div>

                    <div class="mb-3">
                        <label for="current_email" class="form-label">Текущий email</label>
                        <input type="email" class="form-control" id="current_email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="new_email" class="form-label">Новый email</label>
                        <input type="email" class="form-control" id="new_email" name="new_email" required>
                        <div class="invalid-feedback">
                            Введите корректный email адрес
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Изменить email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<style>
    .avatar-preview {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .avatar-container {
        position: relative;
        width: 150px;
        margin: 0 auto;
    }
    .avatar-upload {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
        cursor: pointer;
    }
    .avatar-upload:hover {
        background: #f8f9fa;
    }
    .avatar-delete {
        position: absolute;
        bottom: 0;
        left: 0;
        background: #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
        cursor: pointer;
        color: #dc3545;
        border: none;
        padding: 0;
    }
    .avatar-delete:hover {
        background: #f8f9fa;
        color: #dc3545;
    }
    .profile-info {
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 1.5rem;
    }
    .profile-info p {
        margin-bottom: 0.5rem;
    }
    .profile-info strong {
        color: #495057;
    }
</style>

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
                form.classList.add('was-validated')
            }, false)
        })
    })()

    // Предпросмотр аватара
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
                // Показываем кнопку удаления при загрузке нового аватара
                if (!document.getElementById('deleteAvatar')) {
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'avatar-delete';
                    deleteBtn.id = 'deleteAvatar';
                    deleteBtn.title = 'Удалить аватар';
                    deleteBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
                    document.querySelector('.avatar-container').appendChild(deleteBtn);
                    
                    // Добавляем обработчик удаления
                    deleteBtn.addEventListener('click', function() {
                        document.getElementById('avatarPreview').src = '/images/default-avatar.png';
                        document.getElementById('deleteAvatarInput').value = '1';
                        document.getElementById('avatar').value = '';
                        this.remove();
                    });
                }
            }
            reader.readAsDataURL(file);
        } else {
            // Если файл не выбран (нажата отмена), сбрасываем превью
            document.getElementById('avatarPreview').src = '/images/default-avatar.png';
            const deleteBtn = document.getElementById('deleteAvatar');
            if (deleteBtn) {
                deleteBtn.remove();
            }
        }
    });

    // Удаление аватара (для уже сохраненных аватаров)
    document.getElementById('deleteAvatar')?.addEventListener('click', function() {
        document.getElementById('avatarPreview').src = '/images/default-avatar.png';
        document.getElementById('deleteAvatarInput').value = '1';
        document.getElementById('avatar').value = '';
        this.remove();
    });

    // Валидация формы изменения пароля
    const passwordForm = document.querySelector('#changePasswordModal form');
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const errorAlert = document.getElementById('passwordErrorAlert');
    const successAlert = document.getElementById('passwordSuccessAlert');

    passwordForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!passwordForm.checkValidity()) {
            event.stopPropagation();
            passwordForm.classList.add('was-validated');
            return;
        }

        // Проверка на совпадение нового пароля и подтверждения
        if (newPassword.value !== confirmPassword.value) {
            errorAlert.textContent = 'Пароли не совпадают';
            errorAlert.classList.remove('d-none');
            return;
        }

        // Создаем FormData из формы
        const formData = new FormData(passwordForm);

        // Отправляем AJAX-запрос
        fetch('/change_password', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Показываем сообщение об успехе
                successAlert.textContent = 'Пароль успешно изменен';
                successAlert.classList.remove('d-none');
                errorAlert.classList.add('d-none');
                
                // Очищаем форму
                passwordForm.reset();
                passwordForm.classList.remove('was-validated');
                
                // Закрываем модальное окно через 1.5 секунды
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                    modal.hide();
                }, 1500);
            } else if (data.error) {
                // Показываем ошибку в модальном окне
                errorAlert.textContent = data.error;
                errorAlert.classList.remove('d-none');
                successAlert.classList.add('d-none');
            }
        })
        .catch(error => {
            errorAlert.textContent = 'Произошла ошибка при отправке запроса';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
        });
    });

    // Очистка ошибок при вводе
    currentPassword.addEventListener('input', function() {
        if (newPassword.value !== confirmPassword.value) {
            errorAlert.textContent = 'Пароли не совпадают';
            errorAlert.classList.remove('d-none');
        } else {
            errorAlert.classList.add('d-none');
        }
    });

    newPassword.addEventListener('input', function() {
        if (this.value !== confirmPassword.value) {
            errorAlert.textContent = 'Пароли не совпадают';
            errorAlert.classList.remove('d-none');
        } else {
            errorAlert.classList.add('d-none');
        }
    });

    confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
            errorAlert.textContent = 'Пароли не совпадают';
            errorAlert.classList.remove('d-none');
        } else {
            errorAlert.classList.add('d-none');
        }
    });

    // Очистка ошибок при закрытии модального окна
    document.getElementById('changePasswordModal').addEventListener('hidden.bs.modal', function () {
        passwordForm.reset();
        passwordForm.classList.remove('was-validated');
        errorAlert.classList.add('d-none');
        successAlert.classList.add('d-none');
        confirmPassword.setCustomValidity('');
    });

    // Переключение видимости паролей
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
    togglePasswordVisibility('current_password', 'toggleCurrentPassword');
    togglePasswordVisibility('new_password', 'toggleNewPassword');
    togglePasswordVisibility('confirm_password', 'toggleConfirmPassword');

    // Обработка формы удаления аккаунта
    const deleteAccountForm = document.querySelector('#deleteAccountModal form');
    const confirmation = document.getElementById('confirmation');
    const deleteAccountErrorAlert = document.getElementById('deleteAccountErrorAlert');

    deleteAccountForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!deleteAccountForm.checkValidity()) {
            event.stopPropagation();
            deleteAccountForm.classList.add('was-validated');
            return;
        }

        // Создаем FormData из формы
        const formData = new FormData(deleteAccountForm);

        // Отправляем AJAX-запрос
        fetch('/delete_account', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Перенаправляем на страницу входа
                window.location.href = '/login';
            } else if (data.error) {
                // Показываем ошибку в модальном окне
                deleteAccountErrorAlert.textContent = data.error;
                deleteAccountErrorAlert.classList.remove('d-none');
            }
        })
        .catch(error => {
            deleteAccountErrorAlert.textContent = 'Произошла ошибка при отправке запроса';
            deleteAccountErrorAlert.classList.remove('d-none');
        });
    });

    // Очистка ошибок при закрытии модального окна
    document.getElementById('deleteAccountModal').addEventListener('hidden.bs.modal', function () {
        deleteAccountForm.reset();
        deleteAccountForm.classList.remove('was-validated');
        deleteAccountErrorAlert.classList.add('d-none');
    });

    // Обработка формы изменения email
    const emailForm = document.querySelector('#changeEmailModal form');
    const newEmail = document.getElementById('new_email');
    const emailErrorAlert = document.getElementById('emailErrorAlert');
    const emailSuccessAlert = document.getElementById('emailSuccessAlert');

    emailForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!emailForm.checkValidity()) {
            event.stopPropagation();
            emailForm.classList.add('was-validated');
            return;
        }

        // Создаем FormData из формы
        const formData = new FormData(emailForm);

        // Отправляем AJAX-запрос
        fetch('/change_email', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Показываем сообщение об успехе
                emailSuccessAlert.textContent = 'Инструкции по подтверждению нового email отправлены на указанный адрес';
                emailSuccessAlert.classList.remove('d-none');
                emailErrorAlert.classList.add('d-none');
                
                // Очищаем форму
                emailForm.reset();
                emailForm.classList.remove('was-validated');
                
                // Закрываем модальное окно через 3 секунды
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('changeEmailModal'));
                    modal.hide();
                }, 3000);
            } else if (data.error) {
                // Показываем ошибку в модальном окне
                emailErrorAlert.textContent = data.error;
                emailErrorAlert.classList.remove('d-none');
                emailSuccessAlert.classList.add('d-none');
            }
        })
        .catch(error => {
            emailErrorAlert.textContent = 'Произошла ошибка при отправке запроса';
            emailErrorAlert.classList.remove('d-none');
            emailSuccessAlert.classList.add('d-none');
        });
    });

    // Очистка ошибок при закрытии модального окна
    document.getElementById('changeEmailModal').addEventListener('hidden.bs.modal', function () {
        emailForm.reset();
        emailForm.classList.remove('was-validated');
        emailErrorAlert.classList.add('d-none');
        emailSuccessAlert.classList.add('d-none');
    });
</script> 
