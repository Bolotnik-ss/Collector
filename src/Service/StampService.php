<?php

class StampService {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Создание новой марки
    public function createStamp($collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $theme_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO stamps (
                    collection_id, country_id, type_id, rarity_id, series_id,
                    material_id, theme_id, unit_id, name, code, year, denomination,
                    mintage, description
                ) VALUES (
                    :collection_id, :country_id, :type_id, :rarity_id, :series_id,
                    :material_id, :theme_id, :unit_id, :name, :code, :year, :denomination,
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
                ':theme_id' => $theme_id,
                ':unit_id' => $unit_id,
                ':name' => $name,
                ':code' => $code,
                ':year' => $year,
                ':denomination' => $denomination,
                ':mintage' => $mintage,
                ':description' => $description
            ]);

            $stamp_id = $this->db->lastInsertId();
            $this->db->commit();

            return $stamp_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function saveStampImage($stamp_id, $side, $filename) {
        $table = $side === 'avers' ? 'stamp_averses' : 'stamp_reverses';
        
        // Получаем старое изображение для удаления
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE stamp_id = :stamp_id");
        $stmt->execute([':stamp_id' => $stamp_id]);
        $old_image = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если есть старое изображение, удаляем его запись из БД
        if ($old_image) {
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE stamp_id = :stamp_id");
            $stmt->execute([':stamp_id' => $stamp_id]);
            // Удаляем файл старого изображения
            $this->deleteImage($old_image['image_url']);
        }

        // Сохраняем новое изображение
        $stmt = $this->db->prepare("
            INSERT INTO {$table} (stamp_id, image_url)
            VALUES (:stamp_id, :image_url)
        ");

        $image_url = '/uploads/stamps/' . $filename;
        $stmt->execute([
            ':stamp_id' => $stamp_id,
            ':image_url' => $image_url
        ]);
    }

    // Получение всех марок в коллекции
    public function getStampsByCollection($collection_id, $search = '', $sort = 'year_desc') {
        $query = "
            SELECT DISTINCT s.*, 
                   sc.name as country_name,
                   st.name as type_name,
                   sr.rarity as rarity_name,
                   ss.name as series_name,
                   sm.name as material_name,
                   sth.name as theme_name,
                   su.name as unit_name,
                   (SELECT image_url FROM stamp_averses WHERE stamp_id = s.id LIMIT 1) as avers_image,
                   (SELECT image_url FROM stamp_reverses WHERE stamp_id = s.id LIMIT 1) as revers_image
            FROM stamps s
            LEFT JOIN stamp_countries sc ON s.country_id = sc.id
            LEFT JOIN stamp_types st ON s.type_id = st.id
            LEFT JOIN stamp_rarities sr ON s.rarity_id = sr.id
            LEFT JOIN stamp_series ss ON s.series_id = ss.id
            LEFT JOIN stamp_materials sm ON s.material_id = sm.id
            LEFT JOIN stamp_themes sth ON s.theme_id = sth.id
            LEFT JOIN stamp_units su ON s.unit_id = su.id
            WHERE s.collection_id = :collection_id";

        $params = [':collection_id' => $collection_id];

        if (!empty($search)) {
            $query .= " AND (
                s.name LIKE :search 
                OR s.code LIKE :search 
                OR sc.name LIKE :search 
                OR st.name LIKE :search 
                OR ss.name LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        // Добавляем сортировку
        switch ($sort) {
            case 'year_asc':
                $query .= " ORDER BY s.year ASC, s.name ASC";
                break;
            case 'name_asc':
                $query .= " ORDER BY s.name ASC, s.year DESC";
                break;
            case 'name_desc':
                $query .= " ORDER BY s.name DESC, s.year DESC";
                break;
            case 'year_desc':
            default:
                $query .= " ORDER BY s.year DESC, s.name ASC";
                break;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получение марки по ID
    public function getStampById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, 
                   sc.name as country_name,
                   st.name as type_name,
                   sra.rarity as rarity_name,
                   ss.name as series_name,
                   sm.name as material_name,
                   sth.name as theme_name,
                   su.name as unit_name,
                   sa.image_url as avers_image,
                   sre.image_url as revers_image
            FROM stamps s
            LEFT JOIN stamp_countries sc ON s.country_id = sc.id
            LEFT JOIN stamp_types st ON s.type_id = st.id
            LEFT JOIN stamp_rarities sra ON s.rarity_id = sra.id
            LEFT JOIN stamp_series ss ON s.series_id = ss.id
            LEFT JOIN stamp_materials sm ON s.material_id = sm.id
            LEFT JOIN stamp_themes sth ON s.theme_id = sth.id
            LEFT JOIN stamp_units su ON s.unit_id = su.id
            LEFT JOIN stamp_averses sa ON s.id = sa.stamp_id
            LEFT JOIN stamp_reverses sre ON s.id = sre.stamp_id
            WHERE s.id = :id
        ");

        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Обновление марки
    public function updateStamp($id, $collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $theme_id, $unit_id, $name, $code, $year, $denomination, $mintage, $description) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE stamps SET
                    collection_id = :collection_id,
                    country_id = :country_id,
                    type_id = :type_id,
                    rarity_id = :rarity_id,
                    series_id = :series_id,
                    material_id = :material_id,
                    theme_id = :theme_id,
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
                ':theme_id' => $theme_id,
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

    // Удаление марки
    public function deleteStamp($id) {
        try {
            $this->db->beginTransaction();

            // Получаем информацию о марке для удаления изображений
            $stamp = $this->getStampById($id);
            if ($stamp) {
                // Удаляем изображения
                if ($stamp['avers_image']) {
                    $this->deleteImage($stamp['avers_image']);
                }
                if ($stamp['revers_image']) {
                    $this->deleteImage($stamp['revers_image']);
                }
            }

            // Удаляем записи из таблиц изображений
            $stmt = $this->db->prepare("DELETE FROM stamp_averses WHERE stamp_id = :id");
            $stmt->execute([':id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM stamp_reverses WHERE stamp_id = :id");
            $stmt->execute([':id' => $id]);

            // Удаляем саму марку
            $stmt = $this->db->prepare("DELETE FROM stamps WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Удаление изображения марки
    public function deleteStampImage($stamp_id, $side) {
        $table = $side === 'avers' ? 'stamp_averses' : 'stamp_reverses';
        
        // Получаем информацию об изображении
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE stamp_id = :stamp_id");
        $stmt->execute([':stamp_id' => $stamp_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Удаляем файл
            $this->deleteImage($image['image_url']);
            
            // Удаляем запись из БД
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE stamp_id = :stamp_id");
            $stmt->execute([':stamp_id' => $stamp_id]);
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