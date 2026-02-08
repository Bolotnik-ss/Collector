<?php

class BanknoteCollection {

    public $id;
    public $user_id;
    public $name;
    public $description;
    public $created_at;

    // Создание новой коллекции банкнот
    public static function create($user_id, $name, $description) {
        $db = getDbConnection();
        $stmt = $db->prepare("INSERT INTO banknote_collections (user_id, name, description) 
                              VALUES (:user_id, :name, :description)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        return $db->lastInsertId();
    }

    // Получение коллекции по ID
    public static function findById($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM banknote_collections WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Получение всех коллекций пользователя
    public static function findAllByUser($user_id) {
        $db = getDbConnection();
        $stmt = $db->prepare("
            SELECT 
                c.*,
                COUNT(banknotes.id) as banknote_count
            FROM banknote_collections c
            LEFT JOIN banknotes ON banknotes.collection_id = c.id
            WHERE c.user_id = :user_id
            GROUP BY c.id
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Обновление коллекции
    public static function update($id, $name, $description) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE banknote_collections SET name = :name, description = :description 
                              WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    }

    // Удаление коллекции
    public static function delete($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("DELETE FROM banknote_collections WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
} 