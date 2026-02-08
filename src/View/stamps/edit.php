<?php 
$page_title = 'Редактирование марки';
include __DIR__ . '/../layout/header.php'; 
?>

<?php
$dictionaryService = new StampDictionaryService();
$countries = $dictionaryService->getCountries();
$types = $dictionaryService->getTypes();
$units = $dictionaryService->getUnits();
$series = $dictionaryService->getSeries();
$rarities = $dictionaryService->getRarities();
$materials = $dictionaryService->getMaterials();
$themes = $dictionaryService->getThemes();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Редактирование марки</h2>
                    <form action="/stamp_collection/<?= $collection_id ?>/stamp/<?= $stamp['id'] ?>/update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="collection_id" value="<?= $collection_id ?>">
                        <!-- Название, Страна, Год, Код -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Название марки</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($stamp['name']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="country_id" class="form-label">Страна</label>
                                <div class="input-group">
                                    <select class="form-select" id="country_id" name="country_id" required>
                                        <option value="">Выберите страну</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>" <?= $country['id'] == $stamp['country_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($country['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="year" class="form-label">Год выпуска</label>
                                <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($stamp['year']) ?>" required min="1840" max="<?= date('Y') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="code" class="form-label">Код</label>
                                <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($stamp['code']) ?>">
                            </div>
                        </div>
                        <!-- Номинал, Валюта, Тираж, Материал -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="denomination" class="form-label">Номинал</label>
                                <input type="number" class="form-control" id="denomination" name="denomination" value="<?= htmlspecialchars($stamp['denomination']) ?>" required step="any" min="0.01">
                            </div>
                            <div class="col-md-3">
                                <label for="unit_id" class="form-label">Валюта</label>
                                <div class="input-group">
                                    <select class="form-select" id="unit_id" name="unit_id" required>
                                        <option value="">Выберите валюту</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?= $unit['id'] ?>" <?= $unit['id'] == $stamp['unit_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($unit['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="mintage" class="form-label">Тираж</label>
                                <input type="number" class="form-control" id="mintage" name="mintage" value="<?= htmlspecialchars($stamp['mintage']) ?>" required min="1">
                            </div>
                            <div class="col-md-3">
                                <label for="material_id" class="form-label">Материал</label>
                                <div class="input-group">
                                    <select class="form-select" id="material_id" name="material_id" required>
                                        <option value="">Выберите материал</option>
                                        <?php foreach ($materials as $material): ?>
                                            <option value="<?= $material['id'] ?>" <?= $material['id'] == $stamp['material_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($material['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Тип, Серия, Редкость, Тема -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="type_id" class="form-label">Тип</label>
                                <div class="input-group">
                                    <select class="form-select" id="type_id" name="type_id" required>
                                        <option value="">Выберите тип</option>
                                        <?php foreach ($types as $type): ?>
                                            <option value="<?= $type['id'] ?>" <?= $type['id'] == $stamp['type_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($type['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="series_id" class="form-label">Серия</label>
                                <div class="input-group">
                                    <select class="form-select" id="series_id" name="series_id" required>
                                        <option value="">Выберите серию</option>
                                        <?php foreach ($series as $s): ?>
                                            <option value="<?= $s['id'] ?>" <?= $s['id'] == $stamp['series_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($s['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSeriesModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="rarity_id" class="form-label">Редкость</label>
                                <div class="input-group">
                                    <select class="form-select" id="rarity_id" name="rarity_id" required>
                                        <option value="">Выберите редкость</option>
                                        <?php foreach ($rarities as $rarity): ?>
                                            <option value="<?= $rarity['id'] ?>" <?= $rarity['id'] == $stamp['rarity_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($rarity['rarity']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addRarityModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="theme_id" class="form-label">Тема</label>
                                <div class="input-group">
                                    <select class="form-select" id="theme_id" name="theme_id" required>
                                        <option value="">Выберите тему</option>
                                        <?php foreach ($themes as $theme): ?>
                                            <option value="<?= $theme['id'] ?>" <?= $theme['id'] == $stamp['theme_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($theme['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addThemeModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Описание -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($stamp['description']) ?></textarea>
                        </div>
                        <!-- Изображения -->
                        <div class="row mb-3">
                            <?php if ($stamp['avers_image']): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Текущее изображение аверса</label>
                                    <div class="mb-2">
                                        <img src="<?= htmlspecialchars($stamp['avers_image']) ?>" alt="Аверс" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="delete_avers" name="delete_avers">
                                        <label class="form-check-label" for="delete_avers">
                                            Удалить изображение аверса
                                        </label>
                                    </div>
                                    <label for="avers_image" class="form-label">Новое изображение аверса</label>
                                    <input type="file" class="form-control" id="avers_image" name="avers_image" accept="image/*">
                                </div>
                            <?php else: ?>
                                <div class="col-md-6">
                                    <label for="avers_image" class="form-label">Изображение аверса</label>
                                    <input type="file" class="form-control" id="avers_image" name="avers_image" accept="image/*">
                                </div>
                            <?php endif; ?>

                            <?php if ($stamp['revers_image']): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Текущее изображение реверса</label>
                                    <div class="mb-2">
                                        <img src="<?= htmlspecialchars($stamp['revers_image']) ?>" alt="Реверс" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="delete_revers" name="delete_revers">
                                        <label class="form-check-label" for="delete_revers">
                                            Удалить изображение реверса
                                        </label>
                                    </div>
                                    <label for="revers_image" class="form-label">Новое изображение реверса</label>
                                    <input type="file" class="form-control" id="revers_image" name="revers_image" accept="image/*">
                                </div>
                            <?php else: ?>
                                <div class="col-md-6">
                                    <label for="revers_image" class="form-label">Изображение реверса</label>
                                    <input type="file" class="form-control" id="revers_image" name="revers_image" accept="image/*">
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between">
                            <a href="/stamp_collection/<?= $collection_id ?>" class="btn btn-secondary">
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

</main>

<!-- Модальные окна для добавления записей в справочники -->
<?php
$modals = [
    'Country' => ['title' => 'Добавить новую страну', 'label' => 'Название страны', 'endpoint' => '/api/stamps/dictionary/countries'],
    'Type' => ['title' => 'Добавить новый тип', 'label' => 'Название типа', 'endpoint' => '/api/stamps/dictionary/types'],
    'Unit' => ['title' => 'Добавить новую валюту', 'label' => 'Название валюты', 'endpoint' => '/api/stamps/dictionary/units'],
    'Series' => ['title' => 'Добавить новую серию', 'label' => 'Название серии', 'endpoint' => '/api/stamps/dictionary/series'],
    'Rarity' => ['title' => 'Добавить новую редкость', 'label' => 'Название редкости', 'endpoint' => '/api/stamps/dictionary/rarities'],
    'Material' => ['title' => 'Добавить новый материал', 'label' => 'Название материала', 'endpoint' => '/api/stamps/dictionary/materials'],
    'Theme' => ['title' => 'Добавить новую тему', 'label' => 'Название темы', 'endpoint' => '/api/stamps/dictionary/themes']
];
foreach ($modals as $key => $modal): ?>
<div class="modal fade" id="add<?= $key ?>Modal" tabindex="-1" aria-labelledby="add<?= $key ?>ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add<?= $key ?>ModalLabel"><?= $modal['title'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add<?= $key ?>Form">
                    <div class="mb-3">
                        <label for="<?= strtolower($key) ?>Name" class="form-label"><?= $modal['label'] ?></label>
                        <input type="text" class="form-control" id="<?= strtolower($key) ?>Name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="save<?= $key ?>">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function createDictionaryHandler(key, endpoint) {
    document.getElementById('save' + key).addEventListener('click', function() {
        const name = document.getElementById(key.toLowerCase() + 'Name').value.trim();
        if (!name) {
            alert('Пожалуйста, введите название');
            return;
        }
        const select = document.getElementById(key.toLowerCase() + '_id');
        const options = Array.from(select.options);
        const exists = options.some(option => 
            option.text.toLowerCase() === name.toLowerCase()
        );
        if (exists) {
            alert('Запись с таким названием уже существует в списке');
            return;
        }
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const option = new Option(name, data.id);
                select.add(option);
                select.value = data.id;
                const modal = bootstrap.Modal.getInstance(document.getElementById('add' + key + 'Modal'));
                modal.hide();
                document.getElementById(key.toLowerCase() + 'Name').value = '';
            } else {
                alert(data.message || 'Ошибка при добавлении записи');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при добавлении записи');
        });
    });
    document.getElementById('add' + key + 'Modal').addEventListener('show.bs.modal', function () {
        document.getElementById(key.toLowerCase() + 'Name').value = '';
    });
}
<?php foreach ($modals as $key => $modal): ?>
createDictionaryHandler('<?= $key ?>', '<?= $modal['endpoint'] ?>');
<?php endforeach; ?>
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?> 