<?php

class StampDictionaryService {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Countries
    public function getCountries() {
        $stmt = $this->db->query("SELECT * FROM stamp_countries ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function countryExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stamp_countries WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addCountry($name) {
        if ($this->countryExists($name)) {
            throw new \Exception('Страна с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO stamp_countries (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Types
    public function getTypes() {
        $stmt = $this->db->query("SELECT * FROM stamp_types ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function typeExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stamp_types WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addType($name) {
        if ($this->typeExists($name)) {
            throw new \Exception('Тип марки с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO stamp_types (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Series
    public function getSeries() {
        $stmt = $this->db->query("SELECT * FROM stamp_series ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function seriesExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stamp_series WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addSeries($name) {
        if ($this->seriesExists($name)) {
            throw new \Exception('Серия с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO stamp_series (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Rarities
    public function getRarities() {
        $stmt = $this->db->query("SELECT * FROM stamp_rarities ORDER BY rarity");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function rarityExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stamp_rarities WHERE LOWER(rarity) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addRarity($name) {
        if ($this->rarityExists($name)) {
            throw new \Exception('Редкость с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO stamp_rarities (rarity) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Materials
    public function getMaterials() {
        $stmt = $this->db->query("SELECT * FROM stamp_materials ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function materialExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stamp_materials WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addMaterial($name) {
        if ($this->materialExists($name)) {
            throw new \Exception('Материал с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO stamp_materials (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Themes
    public function getThemes() {
        $stmt = $this->db->query("SELECT * FROM stamp_themes ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function themeExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stamp_themes WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addTheme($name) {
        if ($this->themeExists($name)) {
            throw new \Exception('Тема с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO stamp_themes (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Units (currencies)
    public function getUnits() {
        $stmt = $this->db->query("SELECT * FROM stamp_units ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function unitExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stamp_units WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }
    public function addUnit($name) {
        if ($this->unitExists($name)) {
            throw new \Exception('Валюта с таким названием уже существует');
        }
        $stmt = $this->db->prepare("INSERT INTO stamp_units (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }
} 