<?php


require_once __DIR__ . '/settings.php';


$create_users_table = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255),
        email VARCHAR(255),
        password VARCHAR(255)
    );
";

$create_articles_table = "CREATE TABLE IF NOT EXISTS articles (
        reference INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        price INT,
        quantity INT
    );
";

$pdo->exec("$create_users_table");
$pdo->exec("$create_articles_table");
