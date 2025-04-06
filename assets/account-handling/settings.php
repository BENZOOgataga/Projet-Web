<?php
# la page contient le code de connexion à la base de données

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

$pdo = new PDO("mysql:host=localhost;dbname=webproject;charset=utf8mb4", "root", "root", $options);
