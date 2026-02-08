<?php

class PostcardCollectionController {

    // Просмотр всех коллекций открыток
    public function index() {
        $user_id = $_SESSION['user_id'];
        $postcardCollectionService = new PostcardCollectionService();
        $collections = $postcardCollectionService->getCollections($user_id);

        require __DIR__ . '/../View/postcard_collections/index.php';
    }

    // Просмотр коллекции открыток по ID
    public function show($id) {
        $postcardCollectionService = new PostcardCollectionService();
        $collection = $postcardCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /postcard_collections');
            exit();
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'year_desc';
        $allowed_sort_values = ['year_desc', 'year_asc', 'name_asc', 'name_desc'];
        if (!in_array($sort, $allowed_sort_values)) {
            $sort = 'year_desc';
        }

        $postcardService = new PostcardService();
        $postcards = $postcardService->getPostcardsByCollection($id, $search, $sort);

        require __DIR__ . '/../View/postcard_collections/show.php';
    }

    // Создание новой коллекции
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $user_id = $_SESSION['user_id'];

            $postcardCollectionService = new PostcardCollectionService();
            $postcardCollectionService->createCollection($user_id, $name, $description);

            header('Location: /postcard_collections');
        } else {
            require __DIR__ . '/../View/postcard_collections/create.php';
        }
    }

    // Обновление коллекции
    public function edit($id) {
        $postcardCollectionService = new PostcardCollectionService();
        $collection = $postcardCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /postcard_collections');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];

            $postcardCollectionService->updateCollection($id, $name, $description);

            header('Location: /postcard_collections');
        } else {
            require __DIR__ . '/../View/postcard_collections/edit.php';
        }
    }

    // Удаление коллекции
    public function delete($id) {
        $postcardCollectionService = new PostcardCollectionService();
        $postcardCollectionService->deleteCollection($id);

        header('Location: /postcard_collections');
    }
} 