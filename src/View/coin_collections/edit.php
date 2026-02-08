<?php 
$page_title = 'Редактирование коллекции монет';
include __DIR__ . '/../layout/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Редактирование коллекции монет</h2>

                    <form action="/coin_collection/edit/<?= $collection['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название коллекции</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($collection['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание коллекции</label>
                            <textarea class="form-control" id="description" name="description" 
                                    rows="3"><?= htmlspecialchars($collection['description']) ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/coin_collections" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
