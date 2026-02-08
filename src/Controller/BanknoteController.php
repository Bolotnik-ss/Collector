<?php

class BanknoteController
{
    private $banknoteCollectionService;
    private $banknoteService;
    private $banknoteDictionaryService;

    public function __construct()
    {
        $this->banknoteCollectionService = new BanknoteCollectionService();
        $this->banknoteService = new BanknoteService();
        $this->banknoteDictionaryService = new BanknoteDictionaryService();
    }

    // Метод для отображения коллекций банкнот
    public function index()
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем все коллекции
        $collections = $this->banknoteCollectionService->getCollections($_SESSION['user_id']);

        // Включаем представление для отображения коллекций
        include __DIR__ . '/../View/banknotes/index.php';
    }

    public function show($id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем коллекцию по ID
        $collection = $this->banknoteCollectionService->getCollectionById($id);

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

        // Получаем банкноты в этой коллекции с учетом поиска и сортировки
        $banknotes = $this->banknoteService->getBanknotesByCollection($id, $search, $sort);

        // Включаем представление для отображения коллекции
        require __DIR__ . '/../View/banknotes/show.php';
    }

    public function create($collection_id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем коллекцию
        $collection = $this->banknoteCollectionService->getCollectionById($collection_id);
        if (!$collection) {
            header('Location: /banknote_collections');
            exit();
        }

        // Включаем представление
        include __DIR__ . '/../View/banknotes/create.php';
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
                $this->validateBanknoteData($_POST);

                // Обработка загруженных изображений
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');

                // Создание банкноты
                $banknote_id = $this->banknoteService->createBanknote(
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
                    $this->banknoteService->saveBanknoteImage($banknote_id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->banknoteService->saveBanknoteImage($banknote_id, 'revers', $revers_image);
                }

                header('Location: /banknote_collection/' . $collection_id);
                exit();

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /banknote_collection/' . $collection_id . '/banknote/create');
                exit();
            }
        }
    }

    public function edit($collection_id, $id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Получаем коллекцию и банкноту
        $collection = $this->banknoteCollectionService->getCollectionById($collection_id);
        $banknote = $this->banknoteService->getBanknoteById($id);

        if (!$collection || !$banknote) {
            header('Location: /banknote_collections');
            exit();
        }

        // Включаем представление
        include __DIR__ . '/../View/banknotes/edit.php';
    }

    public function update($collection_id, $id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
                // Валидация входных данных
            $this->validateBanknoteData($_POST);

                // Обработка удаления изображений
                if (isset($_POST['delete_avers'])) {
                    $this->banknoteService->deleteBanknoteImage($id, 'avers');
                }
                if (isset($_POST['delete_revers'])) {
                    $this->banknoteService->deleteBanknoteImage($id, 'revers');
                }

                // Обработка новых изображений
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');

                // Обновление банкноты
            $this->banknoteService->updateBanknote(
                $id,
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

                // Сохранение новых изображений
                if ($avers_image) {
                    $this->banknoteService->saveBanknoteImage($id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->banknoteService->saveBanknoteImage($id, 'revers', $revers_image);
                }

            header('Location: /banknote_collection/' . $collection_id);
                exit();

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /banknote_collection/' . $collection_id . '/banknote/' . $id . '/edit');
                exit();
            }
        }
    }

    public function delete($collection_id, $id)
    {
        // Проверка на авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        try {
            $this->banknoteService->deleteBanknote($id);
            header('Location: /banknote_collection/' . $collection_id);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /banknote_collection/' . $collection_id);
            exit();
        }
    }

    private function validateBanknoteData($data)
    {
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

    private function handleImageUpload($field_name)
    {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$field_name];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Неподдерживаемый тип файла');
        }

        if ($file['size'] > $max_size) {
            throw new Exception('Файл слишком большой');
        }

        $upload_dir = __DIR__ . '/../../public/uploads/banknotes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Ошибка при загрузке файла');
        }

        return $filename;
    }
} 
