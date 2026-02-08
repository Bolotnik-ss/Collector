<?php 
$page_title = 'Создание коллекции монет';
include __DIR__ . '/../layout/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Создание новой коллекции монет</h2>

                    <form action="/coin_collection/create" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название коллекции</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание коллекции</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/coin_collections" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Создать коллекцию
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>
