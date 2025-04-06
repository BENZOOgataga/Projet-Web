<?php
require_once 'settings.php';

try {
    $pdo->exec("INSERT INTO users (username, email, password, is_admin) VALUES
        ('admin', 'admin@localhost.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', TRUE),
        ('user1', 'user1@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', FALSE)");

    $pdo->exec("INSERT INTO articles (name, description, price, original_price, image_url, category, quantity, is_promo) VALUES
        -- Téléphones
        ('iPhone 16 Pro', 'Dernier modèle premium d\'Apple', 1099.99, 1299.99, '16pro.png', 'phones', 25, TRUE),
        ('Samsung Galaxy S24', 'Smartphone Android haut de gamme', 899.99, NULL, 's24.png', 'phones', 40, FALSE),
        ('Google Pixel 8', 'Photographie exceptionnelle et IA avancée', 799.99, 899.99, 'gp8.png', 'phones', 30, TRUE),
        ('Xiaomi 14', 'Rapport qualité-prix imbattable', 649.99, NULL, 'x14.png', 'phones', 45, FALSE),
        ('Nothing Phone 2', 'Design innovant avec interface Glyph', 599.99, 649.99, 'np2.png', 'phones', 20, TRUE),
        
        -- Ordinateurs portables
        ('MacBook Air M2', 'Ordinateur portable léger et puissant', 1199.99, 1299.99, 'm2.png', 'laptops', 15, TRUE),
        ('Dell XPS 15', 'Ordinateur portable professionnel', 1499.99, NULL, 'xps15.png', 'laptops', 20, FALSE),
        ('Lenovo ThinkPad X1', 'Fiabilité et performance pour professionnels', 1399.99, 1599.99, 'x1.png', 'laptops', 18, TRUE),
        ('HP Spectre x360', 'Convertible premium avec écran OLED', 1299.99, NULL, 'x360.png', 'laptops', 22, FALSE),
        ('Asus ZenBook Pro', 'Perfection pour créateurs de contenu', 1599.99, 1799.99, 'zpro.png', 'laptops', 12, TRUE),
        
        -- Gaming
        ('Asus TUF Gaming A17', 'Laptop gaming haute performance', 1799.99, NULL, 'a17.png', 'gaming', 10, FALSE), 
        ('Steam Deck OLED', 'Console portable PC gaming avec écran premium', 549.99, 599.99, 'sdo.png', 'gaming', 12, TRUE),        
        ('Xbox Series X', 'Console puissante avec Game Pass', 479.99, 499.99, 'sx.png', 'gaming', 18, TRUE),
        ('Nvidia RTX 4080', 'Carte graphique pour gaming 4K', 999.99, NULL, '4080.png', 'gaming', 8, FALSE),
        ('Nintendo Switch OLED', 'Console portable avec écran amélioré', 349.99, NULL, 'nso.png', 'gaming', 25, FALSE),
        ('MSI GF63', 'PC gaming pré-assemblé haut de gamme', 2499.99, 2699.99, 'gf63.png', 'gaming', 5, TRUE),
        
        -- Accessoires
        ('Logitech MX Master 3', 'Souris sans fil ergonomique', 89.99, 99.99, 'mx3.png', 'accessories', 50, TRUE),
        ('Logitech G502 HERO', 'Souris sans fil gaming', 89.99, 99.99, 'mx3.png', 'accessories', 50, FALSE),
        ('JBL Xtrem 40W', 'Enceinte JBL sans fil', 89.99, 99.99, 'xtrem40.png', 'accessories', 50, FALSE),
        ('Google Home', 'Assistant personnel intelligent', 89.99, 99.99, 'gh.png', 'accessories', 50, FALSE),
        ('AirPods Pro', 'Écouteurs sans fil avec réduction de bruit', 249.99, 279.99, 'p4.png', 'accessories', 30, TRUE),
        ('Samsung Galaxy Watch 6', 'Montre connectée avec suivi santé', 299.99, NULL, 'gw6.png', 'accessories', 35, FALSE),
        ('Anker PowerCore', 'Batterie externe 20000mAh', 49.99, 59.99, 'anker.png', 'accessories', 60, TRUE),
        ('Keychron K8', 'Clavier mécanique sans fil', 89.99, NULL, 'k8.png', 'accessories', 40, FALSE),
        ('Sony WH-1000XM5', 'Casque audio à réduction de bruit', 349.99, 399.99, 'WH-1000XM5.png', 'accessories', 25, TRUE)");

    $pdo->exec("INSERT INTO orders (user_id, total_amount, status) VALUES
        (2, 1349.98, 'completed'),
        (2, 89.99, 'pending')");

    $pdo->exec("INSERT INTO order_items (order_id, article_id, quantity, price) VALUES
        (1, 1, 1, 1099.99),
        (1, 5, 1, 249.99),
        (2, 5, 1, 89.99)");

    echo "Data inserted successfully";

} catch (PDOException $e) {
    die("Error inserting data: " . $e->getMessage());
}