<?php

class Postcard {

    public $id;
    public $collection_id;
    public $country_id;
    public $type_id;
    public $rarity_id;
    public $series_id;
    public $material_id;
    public $theme_id;
    public $publisher_id;
    public $name;
    public $code;
    public $year;
    public $mintage;
    public $description;

    // Создание новой открытки
    public static function create($collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $theme_id, $publisher_id, $name, $code, $year, $mintage, $description) {
        $db = getDbConnection();
        $stmt = $db->prepare("INSERT INTO postcards (collection_id, country_id, type_id, rarity_id, series_id, material_id, theme_id, publisher_id, name, code, year, mintage, description) 
                              VALUES (:collection_id, :country_id, :type_id, :rarity_id, :series_id, :material_id, :theme_id, :publisher_id, :name, :code, :year, :mintage, :description)");
        $stmt->bindParam(':collection_id', $collection_id);
        $stmt->bindParam(':country_id', $country_id);
        $stmt->bindParam(':type_id', $type_id);
        $stmt->bindParam(':rarity_id', $rarity_id);
        $stmt->bindParam(':series_id', $series_id);
        $stmt->bindParam(':material_id', $material_id);
        $stmt->bindParam(':theme_id', $theme_id);
        $stmt->bindParam(':publisher_id', $publisher_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':mintage', $mintage);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        return $db->lastInsertId();
    }

    // Получение открытки по ID
    public static function findById($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM postcards WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Получение всех открыток в коллекции
    public static function findAllByCollection($collection_id) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM postcards WHERE collection_id = :collection_id");
        $stmt->bindParam(':collection_id', $collection_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Обновление открытки
    public static function update($id, $collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $theme_id, $publisher_id, $name, $code, $year, $mintage, $description) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE postcards SET collection_id = :collection_id, country_id = :country_id, type_id = :type_id, rarity_id = :rarity_id, series_id = :series_id, material_id = :material_id, theme_id = :theme_id, publisher_id = :publisher_id, name = :name, code = :code, year = :year, mintage = :mintage, description = :description WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':collection_id', $collection_id);
        $stmt->bindParam(':country_id', $country_id);
        $stmt->bindParam(':type_id', $type_id);
        $stmt->bindParam(':rarity_id', $rarity_id);
        $stmt->bindParam(':series_id', $series_id);
        $stmt->bindParam(':material_id', $material_id);
        $stmt->bindParam(':theme_id', $theme_id);
        $stmt->bindParam(':publisher_id', $publisher_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':mintage', $mintage);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    }

    // Удаление открытки
    public static function delete($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("DELETE FROM postcards WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
} 