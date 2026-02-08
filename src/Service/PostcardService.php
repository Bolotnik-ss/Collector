<?php

class PostcardService {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Создание новой открытки
    public function createPostcard($collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $theme_id, $publisher_id, $name, $code, $year, $mintage, $description) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO postcards (
                    collection_id, country_id, type_id, rarity_id, series_id,
                    material_id, theme_id, publisher_id, name, code, year, mintage, description
                ) VALUES (
                    :collection_id, :country_id, :type_id, :rarity_id, :series_id,
                    :material_id, :theme_id, :publisher_id, :name, :code, :year, :mintage, :description
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
                ':publisher_id' => $publisher_id,
                ':name' => $name,
                ':code' => $code,
                ':year' => $year,
                ':mintage' => $mintage,
                ':description' => $description
            ]);

            $postcard_id = $this->db->lastInsertId();
            $this->db->commit();

            return $postcard_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function savePostcardImage($postcard_id, $side, $filename) {
        $table = $side === 'avers' ? 'postcard_averses' : 'postcard_reverses';
        
        // Получаем старое изображение для удаления
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE postcard_id = :postcard_id");
        $stmt->execute([':postcard_id' => $postcard_id]);
        $old_image = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если есть старое изображение, удаляем его запись из БД
        if ($old_image) {
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE postcard_id = :postcard_id");
            $stmt->execute([':postcard_id' => $postcard_id]);
            // Удаляем файл старого изображения
            $this->deleteImage($old_image['image_url']);
        }

        // Сохраняем новое изображение
        $stmt = $this->db->prepare("
            INSERT INTO {$table} (postcard_id, image_url)
            VALUES (:postcard_id, :image_url)
        ");

        $image_url = '/uploads/postcards/' . $filename;
        $stmt->execute([
            ':postcard_id' => $postcard_id,
            ':image_url' => $image_url
        ]);
    }

    // Получение всех открыток в коллекции
    public function getPostcardsByCollection($collection_id, $search = '', $sort = 'year_desc') {
        $query = "
            SELECT DISTINCT p.*, 
                   pc.name as country_name,
                   pt.name as type_name,
                   pr.rarity as rarity_name,
                   ps.name as series_name,
                   pm.name as material_name,
                   pth.name as theme_name,
                   pp.name as publisher_name,
                   (SELECT image_url FROM postcard_averses WHERE postcard_id = p.id LIMIT 1) as avers_image,
                   (SELECT image_url FROM postcard_reverses WHERE postcard_id = p.id LIMIT 1) as revers_image
            FROM postcards p
            LEFT JOIN postcard_countries pc ON p.country_id = pc.id
            LEFT JOIN postcard_types pt ON p.type_id = pt.id
            LEFT JOIN postcard_rarities pr ON p.rarity_id = pr.id
            LEFT JOIN postcard_series ps ON p.series_id = ps.id
            LEFT JOIN postcard_materials pm ON p.material_id = pm.id
            LEFT JOIN postcard_themes pth ON p.theme_id = pth.id
            LEFT JOIN postcard_publishers pp ON p.publisher_id = pp.id
            WHERE p.collection_id = :collection_id";

        $params = [':collection_id' => $collection_id];

        if (!empty($search)) {
            $query .= " AND (
                p.name LIKE :search 
                OR p.code LIKE :search 
                OR pc.name LIKE :search 
                OR pt.name LIKE :search 
                OR ps.name LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        // Добавляем сортировку
        switch ($sort) {
            case 'year_asc':
                $query .= " ORDER BY p.year ASC, p.name ASC";
                break;
            case 'name_asc':
                $query .= " ORDER BY p.name ASC, p.year DESC";
                break;
            case 'name_desc':
                $query .= " ORDER BY p.name DESC, p.year DESC";
                break;
            case 'year_desc':
            default:
                $query .= " ORDER BY p.year DESC, p.name ASC";
                break;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получение открытки по ID
    public function getPostcardById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   pc.name as country_name,
                   pt.name as type_name,
                   pra.rarity as rarity_name,
                   ps.name as series_name,
                   pm.name as material_name,
                   pth.name as theme_name,
                   pp.name as publisher_name,
                   pa.image_url as avers_image,
                   pre.image_url as revers_image
            FROM postcards p
            LEFT JOIN postcard_countries pc ON p.country_id = pc.id
            LEFT JOIN postcard_types pt ON p.type_id = pt.id
            LEFT JOIN postcard_rarities pra ON p.rarity_id = pra.id
            LEFT JOIN postcard_series ps ON p.series_id = ps.id
            LEFT JOIN postcard_materials pm ON p.material_id = pm.id
            LEFT JOIN postcard_themes pth ON p.theme_id = pth.id
            LEFT JOIN postcard_publishers pp ON p.publisher_id = pp.id
            LEFT JOIN postcard_averses pa ON p.id = pa.postcard_id
            LEFT JOIN postcard_reverses pre ON p.id = pre.postcard_id
            WHERE p.id = :id
        ");

        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Обновление открытки
    public function updatePostcard($id, $collection_id, $country_id, $type_id, $rarity_id, $series_id, $material_id, $theme_id, $publisher_id, $name, $code, $year, $mintage, $description) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE postcards SET
                    collection_id = :collection_id,
                    country_id = :country_id,
                    type_id = :type_id,
                    rarity_id = :rarity_id,
                    series_id = :series_id,
                    material_id = :material_id,
                    theme_id = :theme_id,
                    publisher_id = :publisher_id,
                    name = :name,
                    code = :code,
                    year = :year,
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
                ':publisher_id' => $publisher_id,
                ':name' => $name,
                ':code' => $code,
                ':year' => $year,
                ':mintage' => $mintage,
                ':description' => $description
            ]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Удаление открытки
    public function deletePostcard($id) {
        try {
            $this->db->beginTransaction();

            // Получаем информацию об открытке для удаления изображений
            $postcard = $this->getPostcardById($id);
            if ($postcard) {
                // Удаляем изображения
                if ($postcard['avers_image']) {
                    $this->deleteImage($postcard['avers_image']);
                }
                if ($postcard['revers_image']) {
                    $this->deleteImage($postcard['revers_image']);
                }
            }

            // Удаляем записи из таблиц изображений
            $stmt = $this->db->prepare("DELETE FROM postcard_averses WHERE postcard_id = :id");
            $stmt->execute([':id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM postcard_reverses WHERE postcard_id = :id");
            $stmt->execute([':id' => $id]);

            // Удаляем саму открытку
            $stmt = $this->db->prepare("DELETE FROM postcards WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Удаление изображения открытки
    public function deletePostcardImage($postcard_id, $side) {
        $table = $side === 'avers' ? 'postcard_averses' : 'postcard_reverses';
        
        // Получаем информацию об изображении
        $stmt = $this->db->prepare("SELECT image_url FROM {$table} WHERE postcard_id = :postcard_id");
        $stmt->execute([':postcard_id' => $postcard_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Удаляем файл
            $this->deleteImage($image['image_url']);
            
            // Удаляем запись из БД
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE postcard_id = :postcard_id");
            $stmt->execute([':postcard_id' => $postcard_id]);
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