<?php
require_once 'auth.php';
require_once '../account-handling/settings.php';

// Handle image upload and product operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product']) || isset($_POST['edit_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
        $category = $_POST['category'];
        $quantity = (int)$_POST['quantity'];
        $is_promo = isset($_POST['is_promo']) ? 1 : 0;

        // Handle image upload
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/products/' . $category . '/';

            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_extension, $allowed_extensions)) {
                $filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_url = $filename;
                }
            }
        }

        if (isset($_POST['add_product'])) {
            $stmt = $pdo->prepare("INSERT INTO articles (name, description, price, original_price, image_url, category, quantity, is_promo) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $original_price, $image_url, $category, $quantity, $is_promo]);
        } else {
            $product_id = (int)$_POST['product_id'];

            // Get current image if no new image uploaded
            if (!$image_url) {
                $stmt = $pdo->prepare("SELECT image_url FROM articles WHERE id = ?");
                $stmt->execute([$product_id]);
                $current_image = $stmt->fetchColumn();
                $image_url = $current_image;
            }

            $stmt = $pdo->prepare("UPDATE articles SET name = ?, description = ?, price = ?, original_price = ?, 
                                 image_url = ?, category = ?, quantity = ?, is_promo = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $original_price, $image_url, $category, $quantity, $is_promo, $product_id]);
        }
    }

    // Handle product deletion
    if (isset($_POST['delete_product'])) {
        $product_id = (int)$_POST['delete_product'];

        // Delete product image first
        $stmt = $pdo->prepare("SELECT image_url, category FROM articles WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $product['image_url']) {
            $image_path = '../images/products/' . $product['category'] . '/' . $product['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$product_id]);
    }
}

// Get products with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total_products = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$total_pages = ceil($total_products / $per_page);

$products = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT ? OFFSET ?");
$products->execute([$per_page, $offset]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - ReverseH4ck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
<div class="admin-layout">
    <div class="admin-sidebar">
        <h2 class="mb-4">Panel Admin</h2>
        <nav>
            <a href="admin_index.php" class="nav-link">Dashboard</a>
            <a href="#" class="nav-link active">Produits</a>
            <a href="orders.php" class="nav-link">Commandes</a>
            <a href="users.php" class="nav-link">Utilisateurs</a>
            <a href="logout.php" class="nav-link text-danger">Se déconnecter</a>
        </nav>
    </div>

    <main class="admin-main p-4">
        <h1 class="mb-4">Gestion des Produits</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Ajouter/Modifier un produit</h5>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="product_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catégorie</label>
                            <select name="category" class="form-select" required>
                                <option value="phones">Téléphones</option>
                                <option value="laptops">Ordinateurs portables</option>
                                <option value="gaming">PC Gaming</option>
                                <option value="accessories">Accessoires</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Prix</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Prix original</label>
                            <input type="number" name="original_price" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Quantité</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_promo" class="form-check-input" id="is_promo">
                                <label class="form-check-label" for="is_promo">En promotion</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary">Ajouter</button>
                    <button type="submit" name="edit_product" class="btn btn-warning" style="display:none;">Modifier</button>
                    <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($product = $products->fetch()): ?>
                        <tr>
                            <td>
                                <?php if ($product['image_url']): ?>
                                    <img src="../images/products/<?php echo $product['category']; ?>/<?php echo $product['image_url']; ?>"
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary" style="width: 50px; height: 50px;"></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><?php echo number_format($product['price'], 2); ?> €</td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $product['is_promo'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $product['is_promo'] ? 'En promo' : 'Normal'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-product"
                                        data-product="<?php echo htmlspecialchars(json_encode($product)); ?>">
                                    Modifier
                                </button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                    <input type="hidden" name="delete_product" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', function() {
            const product = JSON.parse(this.dataset.product);
            const form = document.querySelector('form');

            form.querySelector('[name="product_id"]').value = product.id;
            form.querySelector('[name="name"]').value = product.name;
            form.querySelector('[name="category"]').value = product.category;
            form.querySelector('[name="description"]').value = product.description;
            form.querySelector('[name="price"]').value = product.price;
            form.querySelector('[name="original_price"]').value = product.original_price || '';
            form.querySelector('[name="quantity"]').value = product.quantity;
            form.querySelector('[name="is_promo"]').checked = product.is_promo == 1;

            form.querySelector('[name="add_product"]').style.display = 'none';
            form.querySelector('[name="edit_product"]').style.display = 'inline-block';
        });
    });

    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        const form = this.closest('form');
        form.querySelector('[name="add_product"]').style.display = 'inline-block';
        form.querySelector('[name="edit_product"]').style.display = 'none';
    });
</script>
</body>
</html>