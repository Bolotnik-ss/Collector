<?php 
$page_title = 'Добавление монеты в коллекцию';
include __DIR__ . '/../layout/header.php'; 
?>

<?php
$coinDictionaryService = new CoinDictionaryService();
$countries = $coinDictionaryService->getCountries();
$types = $coinDictionaryService->getTypes();
$units = $coinDictionaryService->getUnits();
$series = $coinDictionaryService->getSeries();
$rarities = $coinDictionaryService->getRarities();
$materials = $coinDictionaryService->getMaterials();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Добавление монеты</h2>
                    
                    <form action="/coin_collection/<?= $collection_id ?>/coin/store" method="POST" enctype="multipart/form-data">
                        <!-- Название, Страна, Год -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label">Название монеты</label>
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
                                <div class="input-group">
                                    <input type="number" class="form-control" id="year" name="year" required min="0" max="<?= date('Y') ?>">
                                    <div class="input-group-text">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="is_bc" name="is_bc">
                                            <label class="form-check-label" for="is_bc">
                                                до н. э.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Код, Номинал, Валюта, Тираж -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="code" class="form-label">Код</label>
                                <input type="text" class="form-control" id="code" name="code">
                            </div>
                            <div class="col-md-3">
                                <label for="denomination" class="form-label">Номинал</label>
                                <input type="number" class="form-control" id="denomination" name="denomination" required step="any" min="0.01">
                            </div>
                            <div class="col-md-3">
                                <label for="unit_id" class="form-label">Валюта</label>
                                <div class="input-group">
                                    <select class="form-select" id="unit_id" name="unit_id" required>
                                        <option value="">Выберите валюту</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="mintage" class="form-label">Тираж</label>
                                <input type="number" class="form-control" id="mintage" name="mintage" required min="1">
                            </div>
                        </div>

                        <!-- Материал, Тип, Серия, Редкость -->
                        <div class="row mb-3">
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
                            <a href="/coin_collection/<?= $collection_id ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Добавить монету
                            </button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const yearInput = document.getElementById('year');
                                const isBcCheckbox = document.getElementById('is_bc');
                                const form = yearInput.closest('form');

                                // Функция для проверки возможности использования "до н.э."
                                function updateBcCheckboxState() {
                                    const yearValue = parseInt(yearInput.value);
                                    if (yearValue === 0) {
                                        isBcCheckbox.checked = false;
                                        isBcCheckbox.disabled = true;
                                    } else {
                                        isBcCheckbox.disabled = false;
                                    }
                                }

                                // Обработчик изменения года
                                yearInput.addEventListener('input', updateBcCheckboxState);

                                // Начальная проверка состояния
                                updateBcCheckboxState();

                                // Обработка отправки формы
                                form.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    const yearValue = parseInt(yearInput.value);
                                    
                                    // Если год не равен 0 и отмечен чекбокс "до н.э."
                                    if (yearValue !== 0 && isBcCheckbox.checked) {
                                        yearInput.value = -yearValue;
                                    }
                                    
                                    form.submit();
                                });
                            });
                        </script>
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
    'Country' => ['title' => 'Добавить новую страну', 'label' => 'Название страны', 'endpoint' => '/api/coins/dictionary/countries'],
    'Type' => ['title' => 'Добавить новый тип', 'label' => 'Название типа', 'endpoint' => '/api/coins/dictionary/types'],
    'Unit' => ['title' => 'Добавить новую валюту', 'label' => 'Название валюты', 'endpoint' => '/api/coins/dictionary/units'],
    'Series' => ['title' => 'Добавить новую серию', 'label' => 'Название серии', 'endpoint' => '/api/coins/dictionary/series'],
    'Rarity' => ['title' => 'Добавить новую редкость', 'label' => 'Название редкости', 'endpoint' => '/api/coins/dictionary/rarities'],
    'Material' => ['title' => 'Добавить новый материал', 'label' => 'Название материала', 'endpoint' => '/api/coins/dictionary/materials']
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
// Функция для создания обработчика событий для каждого справочника
function createDictionaryHandler(key, endpoint) {
    document.getElementById('save' + key).addEventListener('click', function() {
        const name = document.getElementById(key.toLowerCase() + 'Name').value.trim();
        if (!name) {
            alert('Пожалуйста, введите название');
            return;
        }

        // Проверяем, нет ли уже такого значения в списке
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
                // Добавляем новую запись в select
                const option = new Option(name, data.id);
                select.add(option);
                select.value = data.id;
                
                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('add' + key + 'Modal'));
                modal.hide();
                
                // Очищаем поле ввода
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

    // Очищаем поле ввода при открытии модального окна
    document.getElementById('add' + key + 'Modal').addEventListener('show.bs.modal', function () {
        document.getElementById(key.toLowerCase() + 'Name').value = '';
    });
}

// Создаем обработчики для всех справочников
<?php foreach ($modals as $key => $modal): ?>
createDictionaryHandler('<?= $key ?>', '<?= $modal['endpoint'] ?>');
<?php endforeach; ?>
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
