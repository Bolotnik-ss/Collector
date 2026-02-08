<?php

class PostcardDictionaryService {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Countries
    public function getCountries() {
        $stmt = $this->db->query("SELECT * FROM postcard_countries ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function countryExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postcard_countries WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addCountry($name) {
        if ($this->countryExists($name)) {
            throw new \Exception('Страна с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO postcard_countries (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Types
    public function getTypes() {
        $stmt = $this->db->query("SELECT * FROM postcard_types ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function typeExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postcard_types WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addType($name) {
        if ($this->typeExists($name)) {
            throw new \Exception('Тип открытки с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO postcard_types (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Series
    public function getSeries() {
        $stmt = $this->db->query("SELECT * FROM postcard_series ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function seriesExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postcard_series WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addSeries($name) {
        if ($this->seriesExists($name)) {
            throw new \Exception('Серия с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO postcard_series (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Rarities
    public function getRarities() {
        $stmt = $this->db->query("SELECT * FROM postcard_rarities ORDER BY rarity");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function rarityExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postcard_rarities WHERE LOWER(rarity) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addRarity($name) {
        if ($this->rarityExists($name)) {
            throw new \Exception('Редкость с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO postcard_rarities (rarity) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Materials
    public function getMaterials() {
        $stmt = $this->db->query("SELECT * FROM postcard_materials ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function materialExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postcard_materials WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addMaterial($name) {
        if ($this->materialExists($name)) {
            throw new \Exception('Материал с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO postcard_materials (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Themes
    public function getThemes() {
        $stmt = $this->db->query("SELECT * FROM postcard_themes ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function themeExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postcard_themes WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addTheme($name) {
        if ($this->themeExists($name)) {
            throw new \Exception('Тема с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO postcard_themes (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Publishers
    public function getPublishers() {
        $stmt = $this->db->query("SELECT * FROM postcard_publishers ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function publisherExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postcard_publishers WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addPublisher($name) {
        if ($this->publisherExists($name)) {
            throw new \Exception('Издательство с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO postcard_publishers (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }
} 