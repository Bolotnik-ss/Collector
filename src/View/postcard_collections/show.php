<?php 
$page_title = 'Коллекция открыток: ' . htmlspecialchars($collection['name']);
include __DIR__ . '/../layout/header.php'; 
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><?= $page_title ?></h1>
        <div class="btn-group">
            <a href="/postcard_collection/<?= $collection['id'] ?>/postcard/create" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Добавить открытку
            </a>
            <a href="/postcard_collection/edit/<?= $collection['id'] ?>" class="btn btn-secondary">
                <i class="bi bi-pencil"></i> Редактировать коллекцию
            </a>
        </div>
    </div>

    <p class="text-muted mb-4"><?= htmlspecialchars($collection['description']) ?></p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/postcard_collection/<?= $collection['id'] ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">Поиск:</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                           placeholder="Поиск по названию...">
                </div>
                <div class="col-md-4">
                    <label for="sort" class="form-label">Сортировка:</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="year_desc" <?= ($_GET['sort'] ?? '') === 'year_desc' ? 'selected' : '' ?>>По году (по убыванию)</option>
                        <option value="year_asc" <?= ($_GET['sort'] ?? '') === 'year_asc' ? 'selected' : '' ?>>По году (по возрастанию)</option>
                        <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>По названию (А-Я)</option>
                        <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>По названию (Я-А)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($postcards)): ?>
        <div class="text-center py-5">
            <i class="bi bi-card-image display-1 text-muted"></i>
            <h3 class="mt-3">В коллекции пока нет открыток</h3>
            <p class="text-muted">Добавьте свою первую открытку в коллекцию</p>
            <a href="/postcard_collection/<?= $collection['id'] ?>/postcard/create" class="btn btn-primary mt-3">
                <i class="bi bi-plus-lg"></i> Добавить открытку
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($postcards as $postcard): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($postcard['avers_image'] || $postcard['revers_image']): ?>
                            <div class="card-img-top p-3">
                                <div class="row g-2">
                                    <?php if ($postcard['avers_image']): ?>
                                        <div class="col-6">
                                            <img src="<?= htmlspecialchars($postcard['avers_image']) ?>" 
                                                 alt="Аверс" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($postcard['revers_image']): ?>
                                        <div class="col-6">
                                            <img src="<?= htmlspecialchars($postcard['revers_image']) ?>" 
                                                 alt="Реверс" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($postcard['name']) ?></h5>
                            <div class="card-text">
                                <small class="text-muted">
                                    <strong>Страна:</strong> <?= htmlspecialchars($postcard['country_name']) ?><br>
                                    <strong>Год:</strong> <?= $postcard['year'] ?><br>
                                    <?php if ($postcard['code']): ?>
                                        <strong>Код:</strong> <?= htmlspecialchars($postcard['code']) ?><br>
                                    <?php endif; ?>
                                    <?php if ($postcard['mintage']): ?>
                                        <strong>Тираж:</strong> <?= number_format($postcard['mintage'], 0, '.', ' ') ?><br>
                                    <?php endif; ?>
                                    <strong>Материал:</strong> <?= htmlspecialchars($postcard['material_name']) ?><br>
                                    <strong>Тип:</strong> <?= htmlspecialchars($postcard['type_name']) ?><br>
                                    <strong>Серия:</strong> <?= htmlspecialchars($postcard['series_name']) ?><br>
                                    <strong>Редкость:</strong> <?= htmlspecialchars($postcard['rarity_name']) ?><br>
                                    <strong>Тема:</strong> <?= htmlspecialchars($postcard['theme_name']) ?><br>
                                    <strong>Издательство:</strong> <?= htmlspecialchars($postcard['publisher_name']) ?>
                                </small>
                                <?php if ($postcard['description']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?= nl2br(htmlspecialchars($postcard['description'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100">
                                <a href="/postcard_collection/<?= $collection['id'] ?>/postcard/<?= $postcard['id'] ?>/edit" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </a>
                                <a href="/postcard_collection/<?= $collection['id'] ?>/postcard/<?= $postcard['id'] ?>/delete" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Вы уверены, что хотите удалить эту открытку?')">
                                    <i class="bi bi-trash"></i> Удалить
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</main>

<?php include __DIR__ . '/../layout/footer.php'; ?> 