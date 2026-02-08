<?php

class CoinCollectionService {

    // Создание новой коллекции
    public function createCollection($user_id, $name, $description) {
        return CoinCollection::create($user_id, $name, $description);
    }

    // Получение коллекций пользователя
    public function getCollections($user_id) {
        return CoinCollection::findAllByUser($user_id);
    }

    // Получение коллекции по ID
    public function getCollectionById($id) {
        return CoinCollection::findById($id);
    }

    // Обновление коллекции
    public function updateCollection($id, $name, $description) {
        CoinCollection::update($id, $name, $description);
    }

    // Удаление коллекции
    public function deleteCollection($id) {
        CoinCollection::delete($id);
    }

    // Получение количества коллекций пользователя
    public function getCollectionsCount() {
        $user_id = $_SESSION['user_id'];
        $collections = $this->getCollections($user_id);
        return count($collections);
    }
}
