<?php

class BanknoteCollectionController
{
    // Просмотр всех коллекций банкнот
    public function index()
    {
        $user_id = $_SESSION['user_id'];
        $banknoteCollectionService = new BanknoteCollectionService();
        $collections = $banknoteCollectionService->getCollections($user_id);

        require __DIR__ . '/../View/banknote_collections/index.php';
    }

    // Просмотр коллекции банкнот по ID
    public function show($id)
    {
        $banknoteCollectionService = new BanknoteCollectionService();
        $collection = $banknoteCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /banknote_collections');
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

        // Получение банкнот для данной коллекции с учетом поиска и сортировки
        $banknoteService = new BanknoteService();
        $banknotes = $banknoteService->getBanknotesByCollection($id, $search, $sort);

        require __DIR__ . '/../View/banknote_collections/show.php';
    }

    // Создание новой коллекции
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $user_id = $_SESSION['user_id'];

            $banknoteCollectionService = new BanknoteCollectionService();
            $banknoteCollectionService->createCollection($user_id, $name, $description);

            header('Location: /banknote_collections');
        } else {
            require __DIR__ . '/../View/banknote_collections/create.php';
        }
    }

    // Обновление коллекции
    public function edit($id)
    {
        $banknoteCollectionService = new BanknoteCollectionService();
        $collection = $banknoteCollectionService->getCollectionById($id);

        if (!$collection) {
            header('Location: /banknote_collections');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];

            $banknoteCollectionService->updateCollection($id, $name, $description);

                header('Location: /banknote_collections');
        } else {
            require __DIR__ . '/../View/banknote_collections/edit.php';
        }
    }

    // Удаление коллекции
    public function delete($id)
    {
        $banknoteCollectionService = new BanknoteCollectionService();
        $banknoteCollectionService->deleteCollection($id);

            header('Location: /banknote_collections');
    }
} 