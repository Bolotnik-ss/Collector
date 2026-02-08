<?php

class StampController
{
    private $stampCollectionService;
    private $stampService;
    private $stampDictionaryService;

    public function __construct()
    {
        $this->stampCollectionService = new StampCollectionService();
        $this->stampService = new StampService();
        $this->stampDictionaryService = new StampDictionaryService();
    }

    // Список коллекций марок
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collections = $this->stampCollectionService->getCollections($_SESSION['user_id']);
        include __DIR__ . '/../View/collections/index_stamps.php';
    }

    // Просмотр коллекции
    public function show($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collection = $this->stampCollectionService->getCollectionById($id);
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'year_desc';
        $allowed_sort_values = ['year_desc', 'year_asc', 'name_asc', 'name_desc'];
        if (!in_array($sort, $allowed_sort_values)) {
            $sort = 'year_desc';
        }
        $stamps = $this->stampService->getStampsByCollection($id, $search, $sort);
        require __DIR__ . '/../View/collections/stamp_collection.php';
    }

    // Форма создания марки
    public function create($collection_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collection = $this->stampCollectionService->getCollectionById($collection_id);
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }
        include __DIR__ . '/../View/stamps/create.php';
    }

    // Сохранение новой марки
    public function store($collection_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->validateStampData($_POST);
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');
                $stamp_id = $this->stampService->createStamp(
                    $collection_id,
                    $_POST['country_id'],
                    $_POST['type_id'],
                    $_POST['rarity_id'],
                    $_POST['series_id'],
                    $_POST['material_id'],
                    $_POST['theme_id'],
                    $_POST['unit_id'],
                    $_POST['name'],
                    $_POST['code'],
                    $_POST['year'],
                    $_POST['denomination'],
                    $_POST['mintage'],
                    $_POST['description']
                );
                if ($avers_image) {
                    $this->stampService->saveStampImage($stamp_id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->stampService->saveStampImage($stamp_id, 'revers', $revers_image);
                }
                header('Location: /stamp_collection/' . $collection_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /stamp_collection/' . $collection_id . '/stamp/create');
                exit();
            }
        }
    }

    // Форма редактирования марки
    public function edit($collection_id, $stamp_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collection = $this->stampCollectionService->getCollectionById($collection_id);
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }
        $stamp = $this->stampService->getStampById($stamp_id);
        if (!$stamp) {
            http_response_code(404);
            echo "Марка не найдена!";
            exit();
        }
        include __DIR__ . '/../View/stamps/edit.php';
    }

    // Обновление марки
    public function update($stamp_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->validateStampData($_POST);
                if (isset($_POST['delete_avers'])) {
                    $this->stampService->deleteStampImage($stamp_id, 'avers');
                }
                if (isset($_POST['delete_revers'])) {
                    $this->stampService->deleteStampImage($stamp_id, 'revers');
                }
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');
                $this->stampService->updateStamp(
                    $stamp_id,
                    $_POST['collection_id'],
                    $_POST['country_id'],
                    $_POST['type_id'],
                    $_POST['rarity_id'],
                    $_POST['series_id'],
                    $_POST['material_id'],
                    $_POST['theme_id'],
                    $_POST['unit_id'],
                    $_POST['name'],
                    $_POST['code'],
                    $_POST['year'],
                    $_POST['denomination'],
                    $_POST['mintage'],
                    $_POST['description']
                );
                if ($avers_image) {
                    $this->stampService->saveStampImage($stamp_id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->stampService->saveStampImage($stamp_id, 'revers', $revers_image);
                }
                header('Location: /stamp_collection/' . $_POST['collection_id']);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /stamp_collection/' . $_POST['collection_id'] . '/stamp/' . $stamp_id . '/edit');
                exit();
            }
        }
    }

    // Удаление марки
    public function delete($collection_id, $stamp_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $this->stampService->deleteStamp($stamp_id);
        header('Location: /stamp_collection/' . $collection_id);
        exit();
    }

    private function validateStampData($data) {
        $required_fields = [
            'name' => 'Название',
            'country_id' => 'Страна',
            'type_id' => 'Тип',
            'rarity_id' => 'Редкость',
            'series_id' => 'Серия',
            'material_id' => 'Материал',
            'theme_id' => 'Тема',
            'unit_id' => 'Валюта'
        ];
        foreach ($required_fields as $field => $label) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Поле \"{$label}\" обязательно для заполнения");
            }
        }
        $numeric_fields = [
            'year' => 'Год выпуска',
            'denomination' => 'Номинал',
            'mintage' => 'Тираж'
        ];
        foreach ($numeric_fields as $field => $label) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Поле \"{$label}\" обязательно для заполнения");
            }
            if (!is_numeric(trim($data[$field]))) {
                throw new Exception("Поле \"{$label}\" должно быть числом");
            }
        }
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
        $max_size = 5 * 1024 * 1024;
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception("Неподдерживаемый тип файла");
        }
        if ($file['size'] > $max_size) {
            throw new Exception("Файл слишком большой");
        }
        $upload_dir = __DIR__ . '/../../public/uploads/stamps/';
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