<?php

class Banknote {

    public $id;
    public $collection_id;
    public $country_id;
    public $type_id;
    public $rarity_id;
    public $series_id;
    public $material_id;
    public $unit_id;
    public $name;
    public $code;
    public $year;
    public $denomination;
    public $mintage;
    public $description;

    // Создание новой банкноты
    public static function create($collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        $db = getDbConnection();
        $stmt = $db->prepare("INSERT INTO banknotes (collection_id, country_id, type_id, rarity_id, series_id, material_id, unit_id, name, code, year, denomination, mintage, description) 
                              VALUES (:collection_id, :country_id, :type_id, :rarity_id, :series_id, :material_id, :unit_id, :name, :code, :year, :denomination, :mintage, :description)");
        $stmt->bindParam(':collection_id', $collection_id);
        $stmt->bindParam(':country_id', $country_id);
        $stmt->bindParam(':type_id', $type_id);
        $stmt->bindParam(':rarity_id', $rarity_id);
        $stmt->bindParam(':series_id', $series_id);
        $stmt->bindParam(':material_id', $material_id);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':denomination', $denomination);
        $stmt->bindParam(':mintage', $mintage);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        return $db->lastInsertId();
    }

    // Получение банкноты по ID
    public static function findById($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM banknotes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Получение всех банкнот в коллекции
    public static function findAllByCollection($collection_id) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM banknotes WHERE collection_id = :collection_id");
        $stmt->bindParam(':collection_id', $collection_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Обновление банкноты
    public static function update($id, $collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE banknotes SET collection_id = :collection_id, country_id = :country_id, type_id = :type_id, rarity_id = :rarity_id, series_id = :series_id, material_id = :material_id, unit_id = :unit_id, name = :name, code = :code, year = :year, denomination = :denomination, mintage = :mintage, description = :description WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':collection_id', $collection_id);
        $stmt->bindParam(':country_id', $country_id);
        $stmt->bindParam(':type_id', $type_id);
        $stmt->bindParam(':rarity_id', $rarity_id);
        $stmt->bindParam(':series_id', $series_id);
        $stmt->bindParam(':material_id', $material_id);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':denomination', $denomination);
        $stmt->bindParam(':mintage', $mintage);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    }

    // Удаление банкноты
    public static function delete($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("DELETE FROM banknotes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
} 