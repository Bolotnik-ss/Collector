<?php

class StampCollectionController {

    // Просмотр всех коллекций марок
    public function index() {
        $user_id = $_SESSION['user_id'];
        $stampCollectionService = new StampCollectionService();
        $collections = $stampCollectionService->getCollections($user_id);

        require __DIR__ . '/../View/stamp_collections/index.php';
    }

    // Просмотр коллекции марок по ID
    public function show($id) {
        $stampCollectionService = new StampCollectionService();
        $collection = $stampCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /stamp_collections');
            exit();
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'year_desc';
        $allowed_sort_values = ['year_desc', 'year_asc', 'name_asc', 'name_desc'];
        if (!in_array($sort, $allowed_sort_values)) {
            $sort = 'year_desc';
        }

        $stampService = new StampService();
        $stamps = $stampService->getStampsByCollection($id, $search, $sort);

        require __DIR__ . '/../View/stamp_collections/show.php';
    }

    // Создание новой коллекции
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $user_id = $_SESSION['user_id'];

            $stampCollectionService = new StampCollectionService();
            $stampCollectionService->createCollection($user_id, $name, $description);

            header('Location: /stamp_collections');
        } else {
            require __DIR__ . '/../View/stamp_collections/create.php';
        }
    }

    // Обновление коллекции
    public function edit($id) {
        $stampCollectionService = new StampCollectionService();
        $collection = $stampCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /stamp_collections');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];

            $stampCollectionService->updateCollection($id, $name, $description);

            header('Location: /stamp_collections');
        } else {
            require __DIR__ . '/../View/stamp_collections/edit.php';
        }
    }

    // Удаление коллекции
    public function delete($id) {
        $stampCollectionService = new StampCollectionService();
        $stampCollectionService->deleteCollection($id);

        header('Location: /stamp_collections');
    }
} 