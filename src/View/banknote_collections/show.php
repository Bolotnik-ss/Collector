<?php
$page_title = 'Коллекция банкнот: ' . htmlspecialchars($collection['name']);
include __DIR__ . '/../layout/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><?= $page_title ?></h1>
        <div class="btn-group">
            <a href="/banknote_collection/<?= $collection['id'] ?>/banknote/create" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Добавить банкноту
            </a>
            <a href="/banknote_collection/edit/<?= $collection['id'] ?>" class="btn btn-secondary">
                <i class="bi bi-pencil"></i> Редактировать коллекцию
            </a>
        </div>
    </div>

    <p class="text-muted mb-4"><?= htmlspecialchars($collection['description']) ?></p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/banknote_collection/<?= $collection['id'] ?>" class="row g-3 align-items-end">
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

    <?php if (empty($banknotes)): ?>
        <div class="text-center py-5">
            <i class="bi bi-cash display-1 text-muted"></i>
            <h3 class="mt-3">В коллекции пока нет банкнот</h3>
            <p class="text-muted">Добавьте свою первую банкноту в коллекцию</p>
            <a href="/banknote_collection/<?= $collection['id'] ?>/banknote/create" class="btn btn-primary mt-3">
                <i class="bi bi-plus-lg"></i> Добавить банкноту
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($banknotes as $banknote): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($banknote['avers_image'] || $banknote['revers_image']): ?>
                            <div class="card-img-top p-3">
                                <div class="row g-2">
                                    <?php if ($banknote['avers_image']): ?>
                                        <div class="col-12 mb-2">
                                            <img src="<?= htmlspecialchars($banknote['avers_image']) ?>" 
                                                 alt="Аверс" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($banknote['revers_image']): ?>
                                        <div class="col-12">
                                            <img src="<?= htmlspecialchars($banknote['revers_image']) ?>" 
                                                 alt="Реверс" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($banknote['name']) ?></h5>
                            <div class="card-text">
                                <small class="text-muted">
                                    <!-- Основная информация -->
                                    <strong>Страна:</strong> <?= htmlspecialchars($banknote['country_name']) ?><br>
                                    <strong>Год:</strong> <?= $banknote['year'] < 0 ? abs($banknote['year']) . ' г. до н.э.' : $banknote['year'] ?><br>
                                    <?php if ($banknote['code']): ?>
                                        <strong>Код:</strong> <?= htmlspecialchars($banknote['code']) ?><br>
                                    <?php endif; ?>

                                    <!-- Денежная информация -->
                                    <strong>Номинал:</strong> <?= htmlspecialchars($banknote['denomination']) ?> <?= htmlspecialchars($banknote['unit_name']) ?><br>

                                    <!-- Физические характеристики -->
                                    <strong>Материал:</strong> <?= htmlspecialchars($banknote['material_name']) ?><br>
                                    <strong>Тип:</strong> <?= htmlspecialchars($banknote['type_name']) ?><br>

                                    <!-- Коллекционная информация -->
                                    <strong>Серия:</strong> <?= htmlspecialchars($banknote['series_name']) ?><br>
                                    <strong>Редкость:</strong> <?= htmlspecialchars($banknote['rarity_name']) ?><br>
                                    <strong>Тираж:</strong> <?= htmlspecialchars($banknote['mintage']) ?>
                                </small>

                                <?php if ($banknote['description']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?= nl2br(htmlspecialchars($banknote['description'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100">
                                <a href="/banknote_collection/<?= $collection['id'] ?>/banknote/<?= $banknote['id'] ?>/edit" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </a>
                                <a href="/banknote_collection/<?= $collection['id'] ?>/banknote/<?= $banknote['id'] ?>/delete" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Вы уверены, что хотите удалить эту банкноту?')">
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