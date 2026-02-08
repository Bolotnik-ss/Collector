<?php 
$page_title = 'Главная';
include __DIR__ . '/layout/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 text-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <h1 class="display-4 mb-4">Добро пожаловать, <?php echo htmlspecialchars($_SESSION['display_name']); ?>!</h1>
                
                <div class="card mb-5">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Ваш профиль</h5>
                        <div class="row g-4">
                            <div class="col-md-3 mx-auto">
                                <a href="/profile" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-person-circle display-6 d-block mb-2"></i>
                                    Редактировать профиль
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-5">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Ваши коллекции</h5>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <a href="/coin_collections" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-coin display-6 d-block mb-2"></i>
                                    Коллекции монет
                                    <span class="badge bg-primary"><?php echo $coin_collections_count; ?> коллекций</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="/banknote_collections" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-cash-stack display-6 d-block mb-2"></i>
                                    Коллекции банкнот
                                    <span class="badge bg-primary"><?php echo $banknote_collections_count; ?> коллекций</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="/stamp_collections" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-envelope display-6 d-block mb-2"></i>
                                    Коллекции марок
                                    <span class="badge bg-primary"><?php echo $stamp_collections_count; ?> коллекций</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="/postcard_collections" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-card-image display-6 d-block mb-2"></i>
                                    Коллекции открыток
                                    <span class="badge bg-primary"><?php echo $postcard_collections_count; ?> коллекций</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h1 class="display-4 mb-4">Добро пожаловать в мир коллекционирования!</h1>
                <p class="lead mb-5">Создавайте свои коллекции монет, банкнот, марок и открыток и управляйте ими</p>
                <div class="card mb-5">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Начните коллекционировать прямо сейчас</h5>
                        <p class="mb-4">Присоединяйтесь к сообществу коллекционеров и начните создавать свои уникальные коллекции.<br>Если у вас уже есть аккаунт, войдите в систему.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="/register" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                Начать коллекционировать
                            </a>
                            <a href="/login" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Войти в систему
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?> 