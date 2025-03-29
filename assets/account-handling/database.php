<?php
require_once 'settings.php';

try {
    // Création des tables
    $queries = [
        // Table des utilisateurs
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            is_admin BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // Table des articles
        "CREATE TABLE IF NOT EXISTS articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            original_price DECIMAL(10,2),
            image_url VARCHAR(255),
            category VARCHAR(50) NOT NULL,
            quantity INT DEFAULT 0,
            is_promo BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // Table des commandes
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            total_amount DECIMAL(10,2) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",
// Table contact
        "CREATE TABLE IF NOT EXISTS contact (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name_contact VARCHAR(100), 
            email_contact VARCHAR(100), 
            subject_contact VARCHAR(100), 
            message_contact VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",

        // Tables pour les articles de commande
        "CREATE TABLE IF NOT EXISTS order_items (
            order_id INT PRIMARY KEY,
            article_id INT,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (article_id) REFERENCES articles(id)
        )"


    ];

    // On exécute chaque requête pour créer les tables
    foreach ($queries as $query) {
        $pdo->exec($query);
    }

    echo "Database tables created successfully";
} catch (PDOException $e) {
    die("Error creating database tables: " . $e->getMessage());
}
