<?php

class BanknoteService {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Создание новой банкноты
    public function createBanknote($collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO banknotes (
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

            $banknote_id = $this->db->lastInsertId();
            $this->db->commit();

            return $banknote_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function saveBanknoteImage($banknote_id, $side, $filename) {
        $table = $side === 'avers' ? 'banknote_averses' : 'banknote_reverses';
        
        // Получаем старое изображение для удаления
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE banknote_id = :banknote_id");
        $stmt->execute([':banknote_id' => $banknote_id]);
        $old_image = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если есть старое изображение, удаляем его запись из БД
        if ($old_image) {
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE banknote_id = :banknote_id");
            $stmt->execute([':banknote_id' => $banknote_id]);
            // Удаляем файл старого изображения
            $this->deleteImage($old_image['image_url']);
        }

        // Сохраняем новое изображение
        $stmt = $this->db->prepare("
            INSERT INTO {$table} (banknote_id, image_url)
            VALUES (:banknote_id, :image_url)
        ");

        $image_url = '/uploads/banknotes/' . $filename;
        $stmt->execute([
            ':banknote_id' => $banknote_id,
            ':image_url' => $image_url
        ]);
    }

    // Получение всех банкнот в коллекции
    public function getBanknotesByCollection($collection_id, $search = '', $sort = 'year_desc') {
        $query = "
            SELECT DISTINCT b.*, 
                   bc.name as country_name,
                   bt.name as type_name,
                   br.rarity as rarity_name,
                   bs.name as series_name,
                   bm.name as material_name,
                   bu.name as unit_name,
                   (SELECT image_url FROM banknote_averses WHERE banknote_id = b.id LIMIT 1) as avers_image,
                   (SELECT image_url FROM banknote_reverses WHERE banknote_id = b.id LIMIT 1) as revers_image
            FROM banknotes b
            LEFT JOIN banknote_countries bc ON b.country_id = bc.id
            LEFT JOIN banknote_types bt ON b.type_id = bt.id
            LEFT JOIN banknote_rarities br ON b.rarity_id = br.id
            LEFT JOIN banknote_series bs ON b.series_id = bs.id
            LEFT JOIN banknote_materials bm ON b.material_id = bm.id
            LEFT JOIN banknote_units bu ON b.unit_id = bu.id
            WHERE b.collection_id = :collection_id";

        $params = [':collection_id' => $collection_id];

        if (!empty($search)) {
            $query .= " AND (
                b.name LIKE :search 
                OR b.code LIKE :search 
                OR bc.name LIKE :search 
                OR bt.name LIKE :search 
                OR bs.name LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        // Добавляем сортировку
        switch ($sort) {
            case 'year_asc':
                $query .= " ORDER BY b.year ASC, b.name ASC";
                break;
            case 'name_asc':
                $query .= " ORDER BY b.name ASC, b.year DESC";
                break;
            case 'name_desc':
                $query .= " ORDER BY b.name DESC, b.year DESC";
                break;
            case 'year_desc':
            default:
                $query .= " ORDER BY b.year DESC, b.name ASC";
                break;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получение банкноты по ID
    public function getBanknoteById($id) {
        $stmt = $this->db->prepare("
            SELECT b.*, 
                   bc.name as country_name,
                   bt.name as type_name,
                   br.rarity as rarity_name,
                   bs.name as series_name,
                   bm.name as material_name,
                   bu.name as unit_name,
                   ba.image_url as avers_image,
                   brv.image_url as revers_image
            FROM banknotes b
            LEFT JOIN banknote_countries bc ON b.country_id = bc.id
            LEFT JOIN banknote_types bt ON b.type_id = bt.id
            LEFT JOIN banknote_rarities br ON b.rarity_id = br.id
            LEFT JOIN banknote_series bs ON b.series_id = bs.id
            LEFT JOIN banknote_materials bm ON b.material_id = bm.id
            LEFT JOIN banknote_units bu ON b.unit_id = bu.id
            LEFT JOIN banknote_averses ba ON b.id = ba.banknote_id
            LEFT JOIN banknote_reverses brv ON b.id = brv.banknote_id
            WHERE b.id = :id
        ");

        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Обновление банкноты
    public function updateBanknote($id, $collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        try {
        $this->db->beginTransaction();
        
            $stmt = $this->db->prepare("
                UPDATE banknotes SET
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

    // Удаление банкноты
    public function deleteBanknote($id) {
        try {
            $this->db->beginTransaction();

            // Получаем информацию о банкноте для удаления изображений
            $banknote = $this->getBanknoteById($id);
            if ($banknote) {
                // Удаляем изображения
                if ($banknote['avers_image']) {
                    $this->deleteImage($banknote['avers_image']);
            }
                if ($banknote['revers_image']) {
                    $this->deleteImage($banknote['revers_image']);
            }
            }

            // Удаляем записи из таблиц изображений
            $stmt = $this->db->prepare("DELETE FROM banknote_averses WHERE banknote_id = :id");
            $stmt->execute([':id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM banknote_reverses WHERE banknote_id = :id");
            $stmt->execute([':id' => $id]);

            // Удаляем саму банкноту
            $stmt = $this->db->prepare("DELETE FROM banknotes WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Удаление изображения банкноты
    public function deleteBanknoteImage($banknote_id, $side) {
        $table = $side === 'avers' ? 'banknote_averses' : 'banknote_reverses';
        
        // Получаем информацию об изображении
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE banknote_id = :banknote_id");
        $stmt->execute([':banknote_id' => $banknote_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Удаляем файл
            $this->deleteImage($image['image_url']);
            
            // Удаляем запись из БД
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE banknote_id = :banknote_id");
            $stmt->execute([':banknote_id' => $banknote_id]);
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
