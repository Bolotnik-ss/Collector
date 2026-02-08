<?php

class CoinService {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Создание новой монеты
    public function createCoin($collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO coins (
                    collection_id, country_id, type_id, rarity_id, series_id,
                    material_id, unit_id, name, code, year, denomination,
                    mintage, description
                ) VALUES (
                    :collection_id, :country_id, :type_id, :rarity_id, :series_id,
                    :material_id, :unit_id, :name, :code, :year, :denomination,
                    :mintage, :description
                )
            ");

            $stmt->execute([
                ':collection_id' => $collection_id,
                ':country_id' => $country_id,
                ':type_id' => $type_id,
                ':rarity_id' => $rarity_id,
                ':series_id' => $series_id,
                ':material_id' => $material_id,
                ':unit_id' => $unit_id,
                ':name' => $name,
                ':code' => $code,
                ':year' => $year,
                ':denomination' => $denomination,
                ':mintage' => $mintage,
                ':description' => $description
            ]);

            $coin_id = $this->db->lastInsertId();
            $this->db->commit();

            return $coin_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function saveCoinImage($coin_id, $side, $filename) {
        $table = $side === 'avers' ? 'coin_averses' : 'coin_reverses';
        
        // Получаем старое изображение для удаления
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE coin_id = :coin_id");
        $stmt->execute([':coin_id' => $coin_id]);
        $old_image = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если есть старое изображение, удаляем его запись из БД
        if ($old_image) {
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE coin_id = :coin_id");
            $stmt->execute([':coin_id' => $coin_id]);
            // Удаляем файл старого изображения
            $this->deleteImage($old_image['image_url']);
        }

        // Сохраняем новое изображение
        $stmt = $this->db->prepare("
            INSERT INTO {$table} (coin_id, image_url)
            VALUES (:coin_id, :image_url)
        ");

        $image_url = '/uploads/coins/' . $filename;
        $stmt->execute([
            ':coin_id' => $coin_id,
            ':image_url' => $image_url
        ]);
    }

    // Получение всех монет в коллекции
    public function getCoinsByCollection($collection_id, $search = '', $sort = 'year_desc') {
        $query = "
            SELECT DISTINCT c.*, 
                   cc.name as country_name,
                   ct.name as type_name,
                   cr.rarity as rarity_name,
                   cs.name as series_name,
                   cm.name as material_name,
                   cu.name as unit_name,
                   (SELECT image_url FROM coin_averses WHERE coin_id = c.id LIMIT 1) as avers_image,
                   (SELECT image_url FROM coin_reverses WHERE coin_id = c.id LIMIT 1) as revers_image
            FROM coins c
            LEFT JOIN coin_countries cc ON c.country_id = cc.id
            LEFT JOIN coin_types ct ON c.type_id = ct.id
            LEFT JOIN coin_rarities cr ON c.rarity_id = cr.id
            LEFT JOIN coin_series cs ON c.series_id = cs.id
            LEFT JOIN coin_materials cm ON c.material_id = cm.id
            LEFT JOIN coin_units cu ON c.unit_id = cu.id
            WHERE c.collection_id = :collection_id";

        $params = [':collection_id' => $collection_id];

        if (!empty($search)) {
            $query .= " AND (
                c.name LIKE :search 
                OR c.code LIKE :search 
                OR cc.name LIKE :search 
                OR ct.name LIKE :search 
                OR cs.name LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        // Добавляем сортировку
        switch ($sort) {
            case 'year_asc':
                $query .= " ORDER BY c.year ASC, c.name ASC";
                break;
            case 'name_asc':
                $query .= " ORDER BY c.name ASC, c.year DESC";
                break;
            case 'name_desc':
                $query .= " ORDER BY c.name DESC, c.year DESC";
                break;
            case 'year_desc':
            default:
                $query .= " ORDER BY c.year DESC, c.name ASC";
                break;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получение монеты по ID
    public function getCoinById($id) {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   cc.name as country_name,
                   ct.name as type_name,
                   cr.rarity as rarity_name,
                   cs.name as series_name,
                   cm.name as material_name,
                   cu.name as unit_name,
                   ca.image_url as avers_image,
                   crv.image_url as revers_image
            FROM coins c
            LEFT JOIN coin_countries cc ON c.country_id = cc.id
            LEFT JOIN coin_types ct ON c.type_id = ct.id
            LEFT JOIN coin_rarities cr ON c.rarity_id = cr.id
            LEFT JOIN coin_series cs ON c.series_id = cs.id
            LEFT JOIN coin_materials cm ON c.material_id = cm.id
            LEFT JOIN coin_units cu ON c.unit_id = cu.id
            LEFT JOIN coin_averses ca ON c.id = ca.coin_id
            LEFT JOIN coin_reverses crv ON c.id = crv.coin_id
            WHERE c.id = :id
        ");

        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Обновление монеты
    public function updateCoin($id, $collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE coins SET
                    collection_id = :collection_id,
                    country_id = :country_id,
                    type_id = :type_id,
                    rarity_id = :rarity_id,
                    series_id = :series_id,
                    material_id = :material_id,
                    unit_id = :unit_id,
                    name = :name,
                    code = :code,
                    year = :year,
                    denomination = :denomination,
                    mintage = :mintage,
                    description = :description
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $id,
                ':collection_id' => $collection_id,
                ':country_id' => $country_id,
                ':type_id' => $type_id,
                ':rarity_id' => $rarity_id,
                ':series_id' => $series_id,
                ':material_id' => $material_id,
                ':unit_id' => $unit_id,
                ':name' => $name,
                ':code' => $code,
                ':year' => $year,
                ':denomination' => $denomination,
                ':mintage' => $mintage,
                ':description' => $description
            ]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Удаление монеты
    public function deleteCoin($id) {
        try {
            $this->db->beginTransaction();

            // Получаем информацию о монете для удаления изображений
            $coin = $this->getCoinById($id);
            if ($coin) {
                // Удаляем изображения
                if ($coin['avers_image']) {
                    $this->deleteImage($coin['avers_image']);
                }
                if ($coin['revers_image']) {
                    $this->deleteImage($coin['revers_image']);
                }
            }

            // Удаляем записи из таблиц изображений
            $stmt = $this->db->prepare("DELETE FROM coin_averses WHERE coin_id = :id");
            $stmt->execute([':id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM coin_reverses WHERE coin_id = :id");
            $stmt->execute([':id' => $id]);

            // Удаляем саму монету
            $stmt = $this->db->prepare("DELETE FROM coins WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Удаление изображения монеты
    public function deleteCoinImage($coin_id, $side) {
        $table = $side === 'avers' ? 'coin_averses' : 'coin_reverses';
        
        // Получаем информацию об изображении
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE coin_id = :coin_id");
        $stmt->execute([':coin_id' => $coin_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Удаляем файл
            $this->deleteImage($image['image_url']);
            
            // Удаляем запись из БД
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE coin_id = :coin_id");
            $stmt->execute([':coin_id' => $coin_id]);
        }
    }

    private function deleteImage($image_url) {
        if (!empty($image_url)) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $image_url;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
}
