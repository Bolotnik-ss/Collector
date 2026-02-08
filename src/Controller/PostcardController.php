<?php

class PostcardController
{
    private $postcardCollectionService;
    private $postcardService;
    private $postcardDictionaryService;

    public function __construct()
    {
        $this->postcardCollectionService = new PostcardCollectionService();
        $this->postcardService = new PostcardService();
        $this->postcardDictionaryService = new PostcardDictionaryService();
    }

    // Список коллекций открыток
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collections = $this->postcardCollectionService->getCollections($_SESSION['user_id']);
        include __DIR__ . '/../View/collections/index_postcards.php';
    }

    // Просмотр коллекции
    public function show($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collection = $this->postcardCollectionService->getCollectionById($id);
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
        $postcards = $this->postcardService->getPostcardsByCollection($id, $search, $sort);
        require __DIR__ . '/../View/collections/postcard_collection.php';
    }

    // Форма создания открытки
    public function create($collection_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collection = $this->postcardCollectionService->getCollectionById($collection_id);
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }
        include __DIR__ . '/../View/postcards/create.php';
    }

    // Сохранение новой открытки
    public function store($collection_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->validatePostcardData($_POST);
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');
                $postcard_id = $this->postcardService->createPostcard(
                    $collection_id,
                    $_POST['country_id'],
                    $_POST['type_id'],
                    $_POST['rarity_id'],
                    $_POST['series_id'],
                    $_POST['material_id'],
                    $_POST['theme_id'],
                    $_POST['publisher_id'],
                    $_POST['name'],
                    $_POST['code'],
                    $_POST['year'],
                    $_POST['mintage'],
                    $_POST['description']
                );
                if ($avers_image) {
                    $this->postcardService->savePostcardImage($postcard_id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->postcardService->savePostcardImage($postcard_id, 'revers', $revers_image);
                }
                header('Location: /postcard_collection/' . $collection_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /postcard_collection/' . $collection_id . '/postcard/create');
                exit();
            }
        }
    }

    // Форма редактирования открытки
    public function edit($collection_id, $postcard_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $collection = $this->postcardCollectionService->getCollectionById($collection_id);
        if (!$collection) {
            http_response_code(404);
            echo "Коллекция не найдена!";
            exit();
        }
        $postcard = $this->postcardService->getPostcardById($postcard_id);
        if (!$postcard) {
            http_response_code(404);
            echo "Открытка не найдена!";
            exit();
        }
        include __DIR__ . '/../View/postcards/edit.php';
    }

    // Обновление открытки
    public function update($postcard_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->validatePostcardData($_POST);
                if (isset($_POST['delete_avers'])) {
                    $this->postcardService->deletePostcardImage($postcard_id, 'avers');
                }
                if (isset($_POST['delete_revers'])) {
                    $this->postcardService->deletePostcardImage($postcard_id, 'revers');
                }
                $avers_image = $this->handleImageUpload('avers_image');
                $revers_image = $this->handleImageUpload('revers_image');
                $this->postcardService->updatePostcard(
                    $postcard_id,
                    $_POST['collection_id'],
                    $_POST['country_id'],
                    $_POST['type_id'],
                    $_POST['rarity_id'],
                    $_POST['series_id'],
                    $_POST['material_id'],
                    $_POST['theme_id'],
                    $_POST['publisher_id'],
                    $_POST['name'],
                    $_POST['code'],
                    $_POST['year'],
                    $_POST['mintage'],
                    $_POST['description']
                );
                if ($avers_image) {
                    $this->postcardService->savePostcardImage($postcard_id, 'avers', $avers_image);
                }
                if ($revers_image) {
                    $this->postcardService->savePostcardImage($postcard_id, 'revers', $revers_image);
                }
                header('Location: /postcard_collection/' . $_POST['collection_id']);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /postcard_collection/' . $_POST['collection_id'] . '/postcard/' . $postcard_id . '/edit');
                exit();
            }
        }
    }

    // Удаление открытки
    public function delete($collection_id, $postcard_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $this->postcardService->deletePostcard($postcard_id);
        header('Location: /postcard_collection/' . $collection_id);
        exit();
    }

    private function validatePostcardData($data) {
        $required_fields = [
            'name' => 'Название',
            'country_id' => 'Страна',
            'type_id' => 'Тип',
            'rarity_id' => 'Редкость',
            'series_id' => 'Серия',
            'material_id' => 'Материал',
            'theme_id' => 'Тема',
            'publisher_id' => 'Издательство'
        ];
        foreach ($required_fields as $field => $label) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                throw new Exception("Поле \"{$label}\" обязательно для заполнения");
            }
        }
        $numeric_fields = [
            'year' => 'Год выпуска'
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

        // Validate mintage if provided
        if (isset($data['mintage']) && trim($data['mintage']) !== '') {
            if (!is_numeric(trim($data['mintage']))) {
                throw new Exception("Поле \"Тираж\" должно быть числом");
            }
            if (trim($data['mintage']) <= 0) {
                throw new Exception("Тираж должен быть положительным числом");
            }
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
        $upload_dir = __DIR__ . '/../../public/uploads/postcards/';
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