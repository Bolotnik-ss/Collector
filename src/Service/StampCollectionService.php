<?php

class StampCollectionService {
    // Создание новой коллекции
    public function createCollection($user_id, $name, $description) {
        return StampCollection::create($user_id, $name, $description);
    }

    // Получение коллекций пользователя
    public function getCollections($user_id) {
        return StampCollection::findAllByUser($user_id);
    }

    // Получение коллекции по ID
    public function getCollectionById($id) {
        return StampCollection::findById($id);
    }

    // Обновление коллекции
    public function updateCollection($id, $name, $description) {
        StampCollection::update($id, $name, $description);
    }

    // Удаление коллекции
    public function deleteCollection($id) {
        StampCollection::delete($id);
    }

    // Получение количества коллекций пользователя
    public function getCollectionsCount() {
        $user_id = $_SESSION['user_id'];
        $collections = $this->getCollections($user_id);
        return count($collections);
    }
} 