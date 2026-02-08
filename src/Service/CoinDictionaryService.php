<?php

class CoinDictionaryService {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Countries
    public function getCountries() {
        $stmt = $this->db->query("SELECT * FROM coin_countries ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countryExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM coin_countries WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }

    public function addCountry($name) {
        if ($this->countryExists($name)) {
            throw new \Exception('Страна с таким названием уже существует');
        }
        
        $stmt = $this->db->prepare("INSERT INTO coin_countries (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Types
    public function getTypes() {
        $stmt = $this->db->query("SELECT * FROM coin_types ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function typeExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM coin_types WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }

    public function addType($name) {
        if ($this->typeExists($name)) {
            throw new \Exception('Тип монеты с таким названием уже существует');
        }
        
        $stmt = $this->db->prepare("INSERT INTO coin_types (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Units (currencies)
    public function getUnits() {
        $stmt = $this->db->query("SELECT * FROM coin_units ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function unitExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM coin_units WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }

    public function addUnit($name) {
        if ($this->unitExists($name)) {
            throw new \Exception('Валюта с таким названием уже существует');
        }
        
        $stmt = $this->db->prepare("INSERT INTO coin_units (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Series
    public function getSeries() {
        $stmt = $this->db->query("SELECT * FROM coin_series ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function seriesExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM coin_series WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }

    public function addSeries($name) {
        if ($this->seriesExists($name)) {
            throw new \Exception('Серия с таким названием уже существует');
        }
        
        $stmt = $this->db->prepare("INSERT INTO coin_series (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Rarities
    public function getRarities() {
        $stmt = $this->db->query("SELECT * FROM coin_rarities ORDER BY rarity");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function rarityExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM coin_rarities WHERE LOWER(rarity) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }

    public function addRarity($name) {
        if ($this->rarityExists($name)) {
            throw new \Exception('Редкость с таким названием уже существует');
        }
        
        $stmt = $this->db->prepare("INSERT INTO coin_rarities (rarity) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }

    // Materials (metals)
    public function getMaterials() {
        $stmt = $this->db->query("SELECT * FROM coin_materials ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function materialExists($name) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM coin_materials WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => trim($name)]);
        return $stmt->fetchColumn() > 0;
    }

    public function addMaterial($name) {
        if ($this->materialExists($name)) {
            throw new \Exception('Материал с таким названием уже существует');
        }
        
        $stmt = $this->db->prepare("INSERT INTO coin_materials (name) VALUES (:name)");
        $stmt->execute(['name' => trim($name)]);
        return $this->db->lastInsertId();
    }
} 