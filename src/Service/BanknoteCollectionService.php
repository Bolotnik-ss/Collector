<?php

class BanknoteCollectionService
{
    // Создание новой коллекции
    public function createCollection($user_id, $name, $description)
    {
        return BanknoteCollection::create($user_id, $name, $description);
    }

    // Получение коллекций пользователя
    public function getCollections($user_id)
    {
        return BanknoteCollection::findAllByUser($user_id);
    }

    // Получение коллекции по ID
    public function getCollectionById($id)
    {
        return BanknoteCollection::findById($id);
    }

    // Обновление коллекции
    public function updateCollection($id, $name, $description)
    {
        BanknoteCollection::update($id, $name, $description);
    }

    // Удаление коллекции
    public function deleteCollection($id)
    {
        BanknoteCollection::delete($id);
    }

    // Получение количества коллекций пользователя
    public function getCollectionsCount() {
        $user_id = $_SESSION['user_id'];
        $collections = $this->getCollections($user_id);
        return count($collections);
    }
}
