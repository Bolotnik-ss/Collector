<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isSuccess ? 'Пароль успешно изменен' : 'Ошибка сброса пароля'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../layout/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4 text-center">
                        <?php if ($isSuccess): ?>
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h2 class="card-title mt-3 mb-4">Пароль успешно изменен!</h2>
                            
                            <div class="alert alert-success">
                                <h4 class="alert-heading mb-3">Отлично!</h4>
                                <p class="mb-0">Ваш пароль был успешно изменен, и вы автоматически вошли в систему.</p>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <a href="/" class="btn btn-primary">
                                    <i class="bi bi-house-fill"></i> Перейти на главную
                                </a>
                            </div>
                        <?php else: ?>
                            <i class="bi bi-exclamation-circle-fill text-warning" style="font-size: 4rem;"></i>
                            <h2 class="card-title mt-3 mb-4">Ссылка недействительна</h2>
                            
                            <div class="alert alert-warning">
                                <h4 class="alert-heading mb-3">Упс!</h4>
                                <p class="mb-0 text-start">Не удалось сбросить пароль. Возможно:</p>
                                <ul class="text-start mt-2 mb-0">
                                    <li>Ссылка устарела или недействительна</li>
                                    <li>Срок действия ссылки истек (24 часа)</li>
                                    <li>Ссылка уже была использована ранее</li>
                                </ul>
                            </div>

                            <div class="mt-4">
                                <p class="text-muted">
                                    <small>
                                        Если вы недавно запрашивали сброс пароля, попробуйте запросить новую ссылку.<br>
                                        Убедитесь, что вы используете актуальную ссылку из последнего письма.
                                    </small>
                                </p>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <a href="/reset_password" class="btn btn-primary">
                                    <i class="bi bi-arrow-repeat"></i> Запросить новую ссылку
                                </a>
                                <a href="/login" class="btn btn-outline-secondary">
                                    <i class="bi bi-box-arrow-in-right"></i> Перейти на страницу входа
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 