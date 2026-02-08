<?php

class CoinCollectionController {

    // Просмотр всех коллекций монет
    public function index() {
        $user_id = $_SESSION['user_id'];
        $coinCollectionService = new CoinCollectionService();
        $collections = $coinCollectionService->getCollections($user_id);

        require __DIR__ . '/../View/coin_collections/index.php'; // Отображаем коллекции
    }

    // Просмотр коллекции монет по ID
    public function show($id) {
        $coinCollectionService = new CoinCollectionService();
        $collection = $coinCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /coin_collections');
            exit();
        }

        // Получаем параметры поиска и сортировки из GET-запроса
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'year_desc';

        // Проверяем допустимость значения сортировки
        $allowed_sort_values = ['year_desc', 'year_asc', 'name_asc', 'name_desc'];
        if (!in_array($sort, $allowed_sort_values)) {
            $sort = 'year_desc';
        }

        // Получение монет для данной коллекции с учетом поиска и сортировки
        $coinService = new CoinService();
        $coins = $coinService->getCoinsByCollection($id, $search, $sort);

        require __DIR__ . '/../View/coin_collections/show.php'; // Отображаем коллекцию
    }

    // Создание новой коллекции
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $user_id = $_SESSION['user_id'];

            $coinCollectionService = new CoinCollectionService();
            $coinCollectionService->createCollection($user_id, $name, $description);

            header('Location: /coin_collections'); // Перенаправление на главную страницу
        } else {
            require __DIR__ . '/../View/coin_collections/create.php'; // Форма для создания коллекции
        }
    }

    // Обновление коллекции
    public function edit($id) {
        $coinCollectionService = new CoinCollectionService();
        $collection = $coinCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /coin_collections');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];

            $coinCollectionService->updateCollection($id, $name, $description);

            header('Location: /coin_collections'); // Перенаправление на главную страницу
        } else {
            require __DIR__ . '/../View/coin_collections/edit.php'; // Форма для редактирования коллекции
        }
    }

    // Удаление коллекции
    public function delete($id) {
        $coinCollectionService = new CoinCollectionService();
        $coinCollectionService->deleteCollection($id);

        header('Location: /coin_collections'); // Перенаправление на главную страницу
    }
}
