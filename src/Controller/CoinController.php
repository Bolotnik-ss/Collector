<?php

class CoinController
{
    private $coinCollectionService;
    private $coinService;
    private $coinDictionaryService;

    // Конструктор, в котором инициализируем сервисы
    public function __construct()
    {
        $this->coinCollectionService = new CoinCollectionService();
        $this->coinService = new CoinService();
        $this->coinDictionaryService = new CoinDictionaryService();
    }

    // Метод для отображения коллекций монет
    public function index()
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем все коллекции
        $collections = $this->coinCollectionService->getAllCollections();

        // Включаем представление для отображения коллекций
        include __DIR__ . '/../View/collections/index.php';
    }

    // Метод для отображения коллекции
    public function show($id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем коллекцию по ID
        $collection = $this->coinCollectionService->getCollectionById($id);

        // Если коллекция не найдена
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }

        // Получаем параметры поиска и сортировки
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'year_desc';

        // Проверяем допустимость значения сортировки
        $allowed_sort_values = ['year_desc', 'year_asc', 'name_asc', 'name_desc'];
        if (!in_array($sort, $allowed_sort_values)) {
            $sort = 'year_desc';
        }

        // Получаем монеты в этой коллекции с учетом поиска и сортировки
        $coins = $this->coinService->getCoinsByCollection($id, $search, $sort);

        // Включаем представление для отображения коллекции
        require __DIR__ . '/../View/collections/coin_collection.php';
    }

    // Метод для создания монеты в коллекции
    public function create($collection_id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем коллекцию по ID
        $collection = $this->coinCollectionService->getCollectionById($collection_id);

        // Если коллекция не найдена
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }

        // Отображаем форму для добавления монеты
        include __DIR__ . '/../View/coins/create.php';
    }

    public function store($collection_id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Валидация входных данных
                $this->validateCoinData($_POST);

                // Обработка загруженных изображений
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');

                // Создание монеты
                $coin_id = $this->coinService->createCoin(
                    $collection_id,
                    $_POST['country_id'],
                    $_POST['type_id'],
                    $_POST['rarity_id'],
                    $_POST['series_id'],
                    $_POST['material_id'],
                    $_POST['unit_id'],
                    $_POST['name'],
                    $_POST['code'],
                    $_POST['year'],
                    $_POST['denomination'],
                    $_POST['mintage'],
                    $_POST['description']
                );

                // Сохранение изображений
                if ($avers_image) {
                    $this->coinService->saveCoinImage($coin_id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->coinService->saveCoinImage($coin_id, 'revers', $revers_image);
                }

                header('Location: /coin_collection/' . $collection_id);
                exit();

            } catch (Exception $e) {
                // В случае ошибки возвращаемся на форму с сообщением об ошибке
                $_SESSION['error'] = $e->getMessage();
                header('Location: /coin_collection/' . $collection_id . '/coin/create');
                exit();
            }
        }
    }

    public function edit($collection_id, $coin_id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем коллекцию по ID
        $collection = $this->coinCollectionService->getCollectionById($collection_id);

        // Если коллекция не найдена
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }

        // Получаем монету по ID
        $coin = $this->coinService->getCoinById($coin_id);

        // Если монета не найдена
        if (!$coin) {
            http_response_code(404);
            echo "Монета не найдена!";
            exit();
        }   

        // Отображаем форму для редактирования монеты
        include __DIR__ . '/../View/coins/edit.php';
    }
    
    public function update($coin_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Валидация входных данных
                $this->validateCoinData($_POST);

                // Обработка удаления изображений
                if (isset($_POST['delete_avers'])) {
                    $this->coinService->deleteCoinImage($coin_id, 'avers');
                }
                if (isset($_POST['delete_revers'])) {
                    $this->coinService->deleteCoinImage($coin_id, 'revers');
                }

                // Обработка новых изображений
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');

                // Обновление монеты
                $this->coinService->updateCoin(
                    $coin_id,
                    $_POST['collection_id'],
                    $_POST['country_id'],
                    $_POST['type_id'],
                    $_POST['rarity_id'],
                    $_POST['series_id'],
                    $_POST['material_id'],
                    $_POST['unit_id'],
                    $_POST['name'],
                    $_POST['code'],
                    $_POST['year'],
                    $_POST['denomination'],
                    $_POST['mintage'],
                    $_POST['description']
                );

                // Сохранение новых изображений
                if ($avers_image) {
                    $this->coinService->saveCoinImage($coin_id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->coinService->saveCoinImage($coin_id, 'revers', $revers_image);
                }

                header('Location: /coin_collection/' . $_POST['collection_id']);
                exit();

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /coin_collection/' . $_POST['collection_id'] . '/coin/' . $coin_id . '/edit');
                exit();
            }
        }
    }

    public function delete($collection_id, $coin_id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }   

        // Удаляем монету из базы данных
        $this->coinService->deleteCoin($coin_id);

        // Перенаправляем на страницу коллекции
        header('Location: /coin_collection/' . $collection_id);
        exit();
    }

    private function validateCoinData($data) {
        // Проверка наличия обязательных полей
        $required_fields = [
            'name' => 'Название',
            'country_id' => 'Страна',
            'type_id' => 'Тип',
            'rarity_id' => 'Редкость',
            'series_id' => 'Серия',
            'material_id' => 'Материал',
            'unit_id' => 'Валюта'
        ];

        foreach ($required_fields as $field => $label) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Поле \"{$label}\" обязательно для заполнения");
            }
        }

        // Проверка числовых полей
        $numeric_fields = [
            'year' => 'Год выпуска',
            'denomination' => 'Номинал',
            'mintage' => 'Тираж'
        ];

        foreach ($numeric_fields as $field => $label) {
            // Сначала проверяем наличие и непустоту поля
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Поле \"{$label}\" обязательно для заполнения");
            }
            // Затем проверяем, является ли значение числом
            if (!is_numeric(trim($data[$field]))) {
                throw new Exception("Поле \"{$label}\" должно быть числом");
            }
        }

        // Дополнительные проверки для числовых полей
        if ($data['year'] > date('Y')) {
            throw new Exception("Год выпуска не может быть больше текущего года");
        }

        if ($data['denomination'] <= 0) {
            throw new Exception("Номинал должен быть больше нуля");
        }

        if ($data['mintage'] <= 0) {
            throw new Exception("Тираж должен быть больше нуля");
        }
    }

    private function handleImageUpload($field_name) {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$field_name];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Неподдерживаемый тип файла");
        }

        if ($file['size'] > $max_size) {
            throw new Exception("Файл слишком большой");
        }

        $upload_dir = __DIR__ . '/../../public/uploads/coins/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Ошибка при загрузке файла");
        }

        return $filename;
    }

}
