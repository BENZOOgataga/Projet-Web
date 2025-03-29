<?php
require_once 'auth.php';
require_once '../account-handling/settings.php';

// Gestion de la suppression de produit
if (isset($_POST['delete_product'])) {
    $product_id = (int)$_POST['delete_product'];
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$product_id]);
}

// Gestion de la création et de la mise à jour de produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
    $category = trim($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $is_promo = isset($_POST['is_promo']) ? 1 : 0;

    if ($_POST['action'] === 'create') {
        $stmt = $pdo->prepare("
            INSERT INTO articles (name, description, price, original_price, category, quantity, is_promo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $description, $price, $original_price, $category, $quantity, $is_promo]);
    } elseif ($_POST['action'] === 'update') {
        $id = (int)$_POST['product_id'];
        $stmt = $pdo->prepare("
            UPDATE articles 
            SET name = ?, description = ?, price = ?, original_price = ?, 
                category = ?, quantity = ?, is_promo = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $price, $original_price, $category, $quantity, $is_promo, $id]);
    }
}

// Récupération des produits avec pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total_products = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$total_pages = ceil($total_products / $per_page);

$products = $pdo->prepare("
    SELECT * FROM articles 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$products->execute([$per_page, $offset]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Produits - ReverseH4ck</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des Produits</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                Ajouter un produit
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Catégorie</th>
                        <th>Stock</th>
                        <th>Promotion</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($product = $products->fetch()): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>
                                <?php if ($product['original_price']): ?>
                                    <del class="text-muted">€<?php echo number_format($product['original_price'], 2); ?></del><br>
                                <?php endif; ?>
                                €<?php echo number_format($product['price'], 2); ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $product['is_promo'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $product['is_promo'] ? 'Oui' : 'Non'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-product"
                                        data-product="<?php echo htmlspecialchars(json_encode($product)); ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#productModal">
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

<!-- Modal pour ajouter/éditer des produits -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gérer un produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="product_id" value="">

                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix (€)</label>
                            <input type="number" class="form-control" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix original (€)</label>
                            <input type="number" class="form-control" name="original_price" step="0.01">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catégorie</label>
                            <select class="form-select" name="category" required>
                                <option value="phones">Téléphones</option>
                                <option value="laptops">Ordinateurs portables</option>
                                <option value="gaming">PC Gaming</option>
                                <option value="accessories">Accessoires</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantité</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_promo" id="is_promo">
                        <label class="form-check-label" for="is_promo">En promotion</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'édition de produit
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', function() {
                const product = JSON.parse(this.dataset.product);
                const form = document.querySelector('#productModal form');

                form.querySelector('[name="action"]').value = 'update';
                form.querySelector('[name="product_id"]').value = product.id;
                form.querySelector('[name="name"]').value = product.name;
                form.querySelector('[name="description"]').value = product.description;
                form.querySelector('[name="price"]').value = product.price;
                form.querySelector('[name="original_price"]').value = product.original_price || '';
                form.querySelector('[name="category"]').value = product.category;
                form.querySelector('[name="quantity"]').value = product.quantity;
                form.querySelector('[name="is_promo"]').checked = product.is_promo == 1;
            });
        });

        // Réinitialisation du formulaire après la fermeture du modal
        document.querySelector('#productModal').addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            form.reset();
            form.querySelector('[name="action"]').value = 'create';
            form.querySelector('[name="product_id"]').value = '';
        });
    });
</script>
</body>
</html>