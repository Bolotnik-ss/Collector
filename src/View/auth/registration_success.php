<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация успешна</title>
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
                        <i class="bi bi-envelope-check text-success" style="font-size: 4rem;"></i>
                        <h2 class="card-title mt-3 mb-4">Регистрация успешна!</h2>
                        
                        <div class="alert alert-info">
                            <h4 class="alert-heading mb-3">Почти готово!</h4>
                            <p class="mb-0">Мы отправили письмо с подтверждением на адрес <?= htmlspecialchars($maskedEmail) ?>.</p>
                        </div>

                        <div class="mt-4">
                            <h5>Что дальше?</h5>
                            <ol class="text-start">
                                <li>Проверьте вашу почту</li>
                                <li>Найдите письмо от нашей системы</li>
                                <li>Перейдите по ссылке в письме для подтверждения email</li>
                                <li>После подтверждения вы сможете войти в систему</li>
                            </ol>
                        </div>

                        <div class="mt-4">
                            <p class="text-muted">
                                <small>
                                    Если вы не получили письмо в течение нескольких минут, проверьте папку "Спам".<br>
                                </small>
                            </p>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="/login" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Перейти на страницу входа
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 