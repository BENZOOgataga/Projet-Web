<?php
session_start();
require_once '../account-handling/settings.php';
require_once '../utils/notifications.php';

// Check if user is logged in
$userLoggedIn = isset($_SESSION['user_id']);
if (!$userLoggedIn) {
    setFlash('danger', 'Vous devez être connecté pour voir vos commandes.');
    header('Location: ../account-handling/login.php?redirect=panier.php');
    exit;
}

$userId = $_SESSION['user_id'];
$flash = getFlash();
$order = null;
$orderItems = [];

// Check if order_id is provided and is numeric
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $orderId = (int)$_GET['order_id'];

    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ? AND o.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Get order items
        $stmt = $pdo->prepare("
            SELECT oi.*, a.name, a.image_url, a.category
            FROM order_items oi
            JOIN articles a ON oi.article_id = a.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        setFlash('danger', 'Commande non trouvée ou vous n\'êtes pas autorisé à y accéder.');
        header('Location: panier.php');
        exit;
    }
} else {
    setFlash('danger', 'Numéro de commande invalide.');
    header('Location: panier.php');
    exit;
}

$subtotal = 0;
foreach ($orderItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.2;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande - ReverseH4ck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .order-confirmation {
            max-width: 800px;
            margin: 0 auto;
        }
        .order-header {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-status {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .order-items {
            margin: 25px 0;
        }
        .item-row {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        .thank-you-message {
            text-align: center;
            margin: 40px 0;
        }
    </style>
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
                        <a class="nav-link" href="products.php">Produits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.html">À propos</a>
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
                </ul>
            </div>
        </div>
    </nav>
</header>

<section class="py-5">
    <div class="container">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>" role="alert">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="order-confirmation">
            <div class="thank-you-message">
                <h1 class="display-6">Merci pour votre commande!</h1>
                <p class="text-muted">Votre commande a été créée avec succès.</p>
            </div>

            <div class="order-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="h5 mb-1">Commande #<?php echo $order['id']; ?></h2>
                        <p class="text-muted mb-0">Passée le <?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="order-status badge bg-<?php
                        echo match ($order['status']) {
                            'pending' => 'warning',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'secondary'
                        };
                        ?>">
                            <?php echo match ($order['status']) {
                                'pending' => 'En attente',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                                default => 'Inconnue'
                            }; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="order-items">
                <h3 class="h5 mb-3">Articles commandés</h3>

                <?php foreach ($orderItems as $item): ?>
                    <div class="item-row row align-items-center">
                        <div class="col-2 col-md-1">
                            <img src="<?php echo $item['image_url'] ? '../images/products/' . htmlspecialchars($item['category']) . '/' . htmlspecialchars($item['image_url']) : '../images/default.png'; ?>"
                                 class="item-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="col-6 col-md-7">
                            <h4 class="h6 mb-0"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <small class="text-muted"><?php echo htmlspecialchars($item['category']); ?></small>
                        </div>
                        <div class="col-2 text-center">
                            <?php echo $item['quantity']; ?> x
                        </div>
                        <div class="col-2 text-end">
                            <?php echo number_format($item['price'], 2); ?> €
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="order-summary">
                <div class="d-flex justify-content-between mb-2">
                    <span>Sous-total:</span>
                    <span><?php echo number_format($subtotal, 2); ?> €</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>TVA (20%):</span>
                    <span><?php echo number_format($tax, 2); ?> €</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total:</span>
                    <span><?php echo number_format($order['total_amount'], 2); ?> €</span>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="products.php" class="btn btn-outline-secondary">Continuer mes achats</a>
                <a href="panier.php" class="btn btn-primary" style="margin-left: 15px;">Retour au panier</a>
            </div>
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
</body>
</html>