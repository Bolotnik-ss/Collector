<?php 
$page_title = 'Коллекция марок: ' . htmlspecialchars($collection['name']);
include __DIR__ . '/../layout/header.php'; 
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><?= $page_title ?></h1>
        <div class="btn-group">
            <a href="/stamp_collection/<?= $collection['id'] ?>/stamp/create" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Добавить марку
            </a>
            <a href="/stamp_collection/edit/<?= $collection['id'] ?>" class="btn btn-secondary">
                <i class="bi bi-pencil"></i> Редактировать коллекцию
            </a>
        </div>
    </div>

    <p class="text-muted mb-4"><?= htmlspecialchars($collection['description']) ?></p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/stamp_collection/<?= $collection['id'] ?>" class="row g-3 align-items-end">
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

    <?php if (empty($stamps)): ?>
        <div class="text-center py-5">
            <i class="bi bi-envelope display-1 text-muted"></i>
            <h3 class="mt-3">В коллекции пока нет марок</h3>
            <p class="text-muted">Добавьте свою первую марку в коллекцию</p>
            <a href="/stamp_collection/<?= $collection['id'] ?>/stamp/create" class="btn btn-primary mt-3">
                <i class="bi bi-plus-lg"></i> Добавить марку
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($stamps as $stamp): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($stamp['avers_image'] || $stamp['revers_image']): ?>
                            <div class="card-img-top p-3">
                                <div class="row g-2">
                                    <?php if ($stamp['avers_image']): ?>
                                        <div class="col-6">
                                            <img src="<?= htmlspecialchars($stamp['avers_image']) ?>" 
                                                 alt="Аверс" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($stamp['revers_image']): ?>
                                        <div class="col-6">
                                            <img src="<?= htmlspecialchars($stamp['revers_image']) ?>" 
                                                 alt="Реверс" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($stamp['name']) ?></h5>
                            <div class="card-text">
                                <small class="text-muted">
                                    <strong>Страна:</strong> <?= htmlspecialchars($stamp['country_name']) ?><br>
                                    <strong>Год:</strong> <?= $stamp['year'] ?><br>
                                    <?php if ($stamp['code']): ?>
                                        <strong>Код:</strong> <?= htmlspecialchars($stamp['code']) ?><br>
                                    <?php endif; ?>
                                    <strong>Номинал:</strong> <?= htmlspecialchars($stamp['denomination']) ?> <?= htmlspecialchars($stamp['unit_name']) ?><br>
                                    <strong>Материал:</strong> <?= htmlspecialchars($stamp['material_name']) ?><br>
                                    <strong>Тип:</strong> <?= htmlspecialchars($stamp['type_name']) ?><br>
                                    <strong>Серия:</strong> <?= htmlspecialchars($stamp['series_name']) ?><br>
                                    <strong>Редкость:</strong> <?= htmlspecialchars($stamp['rarity_name']) ?><br>
                                    <strong>Тема:</strong> <?= htmlspecialchars($stamp['theme_name']) ?><br>
                                    <strong>Тираж:</strong> <?= htmlspecialchars($stamp['mintage']) ?>
                                </small>
                                <?php if ($stamp['description']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?= nl2br(htmlspecialchars($stamp['description'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100">
                                <a href="/stamp_collection/<?= $collection['id'] ?>/stamp/<?= $stamp['id'] ?>/edit" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </a>
                                <a href="/stamp_collection/<?= $collection['id'] ?>/stamp/<?= $stamp['id'] ?>/delete" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Вы уверены, что хотите удалить эту марку?')">
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