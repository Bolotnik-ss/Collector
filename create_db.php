<?php

// Путь к базе данных
define('DB_PATH', __DIR__ . '/database/database.sqlite');

// Функция для создания базы данных и таблиц
function createDatabase() {
    // Проверяем, существует ли файл базы данных
    if (file_exists(DB_PATH)) {
        echo "База данных уже существует.\n";
        return;
    }

    try {
        // Создаём соединение с базой данных SQLite
        $pdo = new PDO("sqlite:" . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Создаем таблицу пользователей
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(30) NOT NULL UNIQUE,
            email VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(30) NOT NULL,
            display_name VARCHAR(30) NOT NULL,
            birth_date DATE,
            avatar VARCHAR(255),
            info TEXT,
            gender BIT DEFAULT NULL,
            status VARCHAR(7) NOT NULL DEFAULT 'USER',
            created_at DATETIME NOT NULL,
            email_verified BOOLEAN NOT NULL DEFAULT 0,
            email_verification_token VARCHAR(64),
            email_verification_expires DATETIME,
            password_reset_token VARCHAR(64),
            password_reset_expires DATETIME,
            new_email VARCHAR(50),
            email_change_token VARCHAR(64),
            email_change_expires DATETIME,
            CHECK (status IN ('USER', 'SUPPORT'))
        );");

        // Создание таблицы для коллекций монет
        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_collections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );");

        // Создание таблиц для атрибутов монет
        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_countries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_types (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_units (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_series (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_rarities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rarity VARCHAR(30) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_materials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(30) NOT NULL
        );");

        // Создание таблицы монет
        $pdo->exec("CREATE TABLE IF NOT EXISTS coins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            collection_id INTEGER NOT NULL,
            country_id INTEGER NOT NULL,
            type_id INTEGER NOT NULL,
            rarity_id INTEGER NOT NULL,
            series_id INTEGER NOT NULL,
            material_id INTEGER NOT NULL,
            unit_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20),
            year INTEGER NOT NULL,
            denomination INTEGER NOT NULL,
            mintage INTEGER NOT NULL,
            description TEXT,
            FOREIGN KEY (collection_id) REFERENCES coin_collections(id),
            FOREIGN KEY (country_id) REFERENCES coin_countries(id),
            FOREIGN KEY (type_id) REFERENCES coin_types(id),
            FOREIGN KEY (rarity_id) REFERENCES coin_rarities(id),
            FOREIGN KEY (series_id) REFERENCES coin_series(id),
            FOREIGN KEY (material_id) REFERENCES coin_materials(id),
            FOREIGN KEY (unit_id) REFERENCES coin_units(id)
        );");

        // Создание таблицы аверса монеты
        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_averses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            coin_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (coin_id) REFERENCES coins(id)
        );");

        // Создание таблицы реверса монеты
        $pdo->exec("CREATE TABLE IF NOT EXISTS coin_reverses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            coin_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (coin_id) REFERENCES coins(id)
        );");

        // Создание таблицы для коллекций банкнот
        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_collections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );");

        // Создание таблиц для атрибутов банкнот
        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_countries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_types (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_units (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_series (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_rarities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rarity VARCHAR(30) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_materials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(30) NOT NULL
        );");

        // Создание таблицы банкнот
        $pdo->exec("CREATE TABLE IF NOT EXISTS banknotes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            collection_id INTEGER NOT NULL,
            country_id INTEGER NOT NULL,
            type_id INTEGER NOT NULL,
            rarity_id INTEGER NOT NULL,
            series_id INTEGER NOT NULL,
            material_id INTEGER NOT NULL,
            unit_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20),
            year INTEGER NOT NULL,
            denomination INTEGER NOT NULL,
            mintage INTEGER NOT NULL,
            description TEXT,
            FOREIGN KEY (collection_id) REFERENCES banknote_collections(id),
            FOREIGN KEY (country_id) REFERENCES banknote_countries(id),
            FOREIGN KEY (type_id) REFERENCES banknote_types(id),
            FOREIGN KEY (series_id) REFERENCES banknote_series(id),
            FOREIGN KEY (rarity_id) REFERENCES banknote_rarities(id),
            FOREIGN KEY (material_id) REFERENCES banknote_materials(id),
            FOREIGN KEY (unit_id) REFERENCES banknote_units(id)
        );");

        // Создание таблицы аверса банкноты
        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_averses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            banknote_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (banknote_id) REFERENCES banknotes(id)
        );");

        // Создание таблицы реверса банкноты
        $pdo->exec("CREATE TABLE IF NOT EXISTS banknote_reverses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            banknote_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (banknote_id) REFERENCES banknotes(id)
        );");

        // Создание таблицы для коллекций марок
        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_collections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );");

        // Создание таблиц для атрибутов марок
        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_countries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_types (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_series (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_rarities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rarity VARCHAR(30) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_materials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(30) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_themes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_units (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL
        );");

        // Создание таблицы марок
        $pdo->exec("CREATE TABLE IF NOT EXISTS stamps (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            collection_id INTEGER NOT NULL,
            country_id INTEGER NOT NULL,
            type_id INTEGER NOT NULL,
            rarity_id INTEGER NOT NULL,
            series_id INTEGER NOT NULL,
            material_id INTEGER NOT NULL,
            theme_id INTEGER NOT NULL,
            unit_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20),
            year INTEGER NOT NULL,
            denomination INTEGER NOT NULL,
            mintage INTEGER NOT NULL,
            description TEXT,
            FOREIGN KEY (collection_id) REFERENCES stamp_collections(id),
            FOREIGN KEY (country_id) REFERENCES stamp_countries(id),
            FOREIGN KEY (type_id) REFERENCES stamp_types(id),
            FOREIGN KEY (rarity_id) REFERENCES stamp_rarities(id),
            FOREIGN KEY (series_id) REFERENCES stamp_series(id),
            FOREIGN KEY (material_id) REFERENCES stamp_materials(id),
            FOREIGN KEY (theme_id) REFERENCES stamp_themes(id),
            FOREIGN KEY (unit_id) REFERENCES stamp_units(id)
        );");

        // Создание таблицы аверса марки
        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_averses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            stamp_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (stamp_id) REFERENCES stamps(id)
        );");

        // Создание таблицы реверса марки
        $pdo->exec("CREATE TABLE IF NOT EXISTS stamp_reverses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            stamp_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (stamp_id) REFERENCES stamps(id)
        );");

        // Создание таблицы для коллекций открыток
        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_collections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );");

        // Создание таблиц для атрибутов открыток
        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_countries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_types (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_series (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_rarities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rarity VARCHAR(30) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_materials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(30) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_themes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_publishers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL
        );");

        // Создание таблицы открыток
        $pdo->exec("CREATE TABLE IF NOT EXISTS postcards (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            collection_id INTEGER NOT NULL,
            country_id INTEGER NOT NULL,
            type_id INTEGER NOT NULL,
            rarity_id INTEGER NOT NULL,
            series_id INTEGER NOT NULL,
            material_id INTEGER NOT NULL,
            theme_id INTEGER NOT NULL,
            publisher_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20),
            year INTEGER NOT NULL,
            mintage INTEGER,
            description TEXT,
            FOREIGN KEY (collection_id) REFERENCES postcard_collections(id),
            FOREIGN KEY (country_id) REFERENCES postcard_countries(id),
            FOREIGN KEY (type_id) REFERENCES postcard_types(id),
            FOREIGN KEY (rarity_id) REFERENCES postcard_rarities(id),
            FOREIGN KEY (series_id) REFERENCES postcard_series(id),
            FOREIGN KEY (material_id) REFERENCES postcard_materials(id),
            FOREIGN KEY (theme_id) REFERENCES postcard_themes(id),
            FOREIGN KEY (publisher_id) REFERENCES postcard_publishers(id)
        );");

        // Создание таблицы аверса открытки
        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_averses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            postcard_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (postcard_id) REFERENCES postcards(id)
        );");

        // Создание таблицы реверса открытки
        $pdo->exec("CREATE TABLE IF NOT EXISTS postcard_reverses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            postcard_id INTEGER NOT NULL,
            note TEXT,
            image_url VARCHAR(255),
            FOREIGN KEY (postcard_id) REFERENCES postcards(id)
        );");

        echo "База данных и таблицы успешно созданы.\n";

    } catch (PDOException $e) {
        echo "Ошибка при создании базы данных: " . $e->getMessage() . "\n";
    }
}

// Запускаем создание базы данных
createDatabase();
