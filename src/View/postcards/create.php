<?php 
$page_title = 'Добавление открытки в коллекцию';
include __DIR__ . '/../layout/header.php'; 
?>

<?php
$dictionaryService = new PostcardDictionaryService();
$countries = $dictionaryService->getCountries();
$types = $dictionaryService->getTypes();
$series = $dictionaryService->getSeries();
$rarities = $dictionaryService->getRarities();
$materials = $dictionaryService->getMaterials();
$themes = $dictionaryService->getThemes();
$publishers = $dictionaryService->getPublishers();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Добавление открытки</h2>
                    <form action="/postcard_collection/<?= $collection_id ?>/postcard/store" method="POST" enctype="multipart/form-data">
                        <!-- Название, Страна, Год -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label">Название открытки</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="country_id" class="form-label">Страна</label>
                                <div class="input-group">
                                    <select class="form-select" id="country_id" name="country_id" required>
                                        <option value="">Выберите страну</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="year" class="form-label">Год выпуска</label>
                                <input type="number" class="form-control" id="year" name="year" required min="1860" max="<?= date('Y') ?>">
                            </div>
                        </div>
                        <!-- Код, Тираж, Материал, Тип -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="code" class="form-label">Код</label>
                                <input type="text" class="form-control" id="code" name="code">
                            </div>
                            <div class="col-md-3">
                                <label for="mintage" class="form-label">Тираж</label>
                                <input type="number" class="form-control" id="mintage" name="mintage" min="1">
                        </div>
                            <div class="col-md-3">
                                <label for="material_id" class="form-label">Материал</label>
                                <div class="input-group">
                                    <select class="form-select" id="material_id" name="material_id" required>
                                        <option value="">Выберите материал</option>
                                        <?php foreach ($materials as $material): ?>
                                            <option value="<?= $material['id'] ?>"><?= htmlspecialchars($material['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="type_id" class="form-label">Тип</label>
                                <div class="input-group">
                                    <select class="form-select" id="type_id" name="type_id" required>
                                        <option value="">Выберите тип</option>
                                        <?php foreach ($types as $type): ?>
                                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Серия, Редкость, Тема, Издательство -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="series_id" class="form-label">Серия</label>
                                <div class="input-group">
                                    <select class="form-select" id="series_id" name="series_id" required>
                                        <option value="">Выберите серию</option>
                                        <?php foreach ($series as $s): ?>
                                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
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
                                            <option value="<?= $rarity['id'] ?>"><?= htmlspecialchars($rarity['rarity']) ?></option>
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
                                            <option value="<?= $theme['id'] ?>"><?= htmlspecialchars($theme['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addThemeModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="publisher_id" class="form-label">Издательство</label>
                                <div class="input-group">
                                    <select class="form-select" id="publisher_id" name="publisher_id" required>
                                        <option value="">Выберите издательство</option>
                                        <?php foreach ($publishers as $publisher): ?>
                                            <option value="<?= $publisher['id'] ?>"><?= htmlspecialchars($publisher['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Описание -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <!-- Изображения -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="avers_image" class="form-label">Изображение аверса</label>
                                <input type="file" class="form-control" id="avers_image" name="avers_image" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label for="revers_image" class="form-label">Изображение реверса</label>
                                <input type="file" class="form-control" id="revers_image" name="revers_image" accept="image/*">
                            </div>
                        </div>
                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between">
                            <a href="/postcard_collection/<?= $collection_id ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Добавить открытку
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
    'Country' => ['title' => 'Добавить новую страну', 'label' => 'Название страны', 'endpoint' => '/api/postcards/dictionary/countries'],
    'Type' => ['title' => 'Добавить новый тип', 'label' => 'Название типа', 'endpoint' => '/api/postcards/dictionary/types'],
    'Series' => ['title' => 'Добавить новую серию', 'label' => 'Название серии', 'endpoint' => '/api/postcards/dictionary/series'],
    'Rarity' => ['title' => 'Добавить новую редкость', 'label' => 'Название редкости', 'endpoint' => '/api/postcards/dictionary/rarities'],
    'Material' => ['title' => 'Добавить новый материал', 'label' => 'Название материала', 'endpoint' => '/api/postcards/dictionary/materials'],
    'Theme' => ['title' => 'Добавить новую тему', 'label' => 'Название темы', 'endpoint' => '/api/postcards/dictionary/themes'],
    'Publisher' => ['title' => 'Добавить новое издательство', 'label' => 'Название издательства', 'endpoint' => '/api/postcards/dictionary/publishers']
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