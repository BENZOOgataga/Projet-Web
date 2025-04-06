<?php
require_once '../account-handling/settings.php';

// récupère la catégorie, prend 'toutes'
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// requete sql selon la catégorie, pdo pour lier avec la db
$sql = "SELECT * FROM articles WHERE 1=1";
if ($category !== 'all') {
    $sql .= " AND category = :category";
}
$sql .= " ORDER BY created_at DESC";


try {
    $stmt = $pdo->prepare($sql);
    if ($category !== 'all') {
        $stmt->bindParam(':category', $category);
    }
    // éxecute la requête
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    // message d'erreur si la requête échoue
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits - ReverseH4ck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<header class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">
                <img src="../images/logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Produits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.php">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="panier.php">Panier</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../account-handling/login.php">Mon Compte</a>
                    </li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../../assets/admin/admin_index.php">Admin Dashboard</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<section class="py-5">
    <div class="container">
        <h1 class="text-center mb-4">Nos Produits</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-center gap-2">
                    <a href="?category=all" class="btn <?php echo $category === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        Tous
                    </a>
                    <a href="?category=phones" class="btn <?php echo $category === 'phones' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        Téléphones
                    </a>
                    <a href="?category=laptops" class="btn <?php echo $category === 'laptops' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        Ordinateurs portables
                    </a>
                    <a href="?category=gaming" class="btn <?php echo $category === 'gaming' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        PC Gaming
                    </a>
                    <a href="?category=accessories" class="btn <?php echo $category === 'accessories' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        Accessoires
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="product-card">
                        <?php if ($product['is_promo']): ?>
                            <div class="promo-badge">Promo</div>
                        <?php endif; ?>
                        <img src="<?php echo (!empty($product['image_url']))
                            ? '../images/products/' . htmlspecialchars($product['category']) . '/' . htmlspecialchars($product['image_url'])
                            : '../images/default.png'; ?>"
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="price">
                                <?php if ($product['original_price']): ?>
                                    <span class="original-price">€<?php echo number_format($product['original_price'], 2); ?></span>
                                <?php endif; ?>
                                <span class="<?php echo $product['original_price'] ? 'sale-price' : 'current-price'; ?>">
                                    €<?php echo number_format($product['price'], 2); ?>
                                </span>
                            </div>
                            <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>"
                                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                    data-product-price="<?php echo $product['price']; ?>"
                                    data-product-category="<?php echo htmlspecialchars($product['category']); ?>"
                                    data-product-image="<?php echo htmlspecialchars($product['image_url']); ?>">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="text-center">
            <strong>ReverseH4ck</strong> &copy; Tous Droits Réservés
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            // récupère info produits
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;
            const productCategory = this.dataset.productCategory;
            const productImage = this.dataset.productImage;
// notification article ajouté (alert)
            addToCart(productId, productName, productPrice, productCategory, productImage);
            alert('Produit ajouté au panier !');
        });
    });
    // ajoute au panier, regarde dans le local storage et cherche si le compte possède un panier, ajoute +1 si existe pas
    function addToCart(id, name, price, category, image) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ id, name, price, category, image, quantity: 1 });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
    }
</script>
</body>
</html>