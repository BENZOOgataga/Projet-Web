<?php
session_start();
require_once '../account-handling/settings.php';
require_once '../utils/notifications.php';

$userLoggedIn = isset($_SESSION['user_id']);
$userId = $userLoggedIn ? $_SESSION['user_id'] : null;
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (!$userLoggedIn) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour finaliser votre commande.']);
        exit;
    }

    $cartData = $_POST['cart_data'] ?? '[]';
    $cartItems = json_decode($cartData, true);

    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'message' => 'Votre panier est vide.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // calcul montant total
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // ajoute tax 20%
        $totalAmount += $totalAmount * 0.2;

        // créer une commande prépare et éxecute
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$userId, $totalAmount]);
        $orderId = $pdo->lastInsertId();

        // ajoute les produits dans la commande
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, article_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }

        // efface le panier de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ?");
        $stmt->execute([$userId]);

        $pdo->commit();
        echo json_encode(['success' => true, 'order_id' => $orderId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Une erreur s\'est produite lors de la création de votre commande.']);
    }

    exit;
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - ReverseH4ck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }
        .cart-item.removing {
            transform: translateX(100px);
            opacity: 0;
        }
        .cart-item.new-item {
            animation: fadeIn 0.5s ease;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-control input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            margin: 0 5px;
            padding: 2px 5px;
        }
        .quantity-control button {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #f1f1f1;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        .quantity-control button:hover {
            background-color: #e0e0e0;
            transform: scale(1.1);
        }
        .remove-item {
            transition: all 0.2s ease;
        }
        .remove-item:hover {
            transform: scale(1.05);
        }
        .empty-cart {
            text-align: center;
            padding: 3rem 0;
        }
        .empty-cart .message {
            margin: 1rem 0 2rem;
            color: #666;
        }
        .cart-summary {
            background-color: #f9f9f9;
            padding: 1.5rem;
            border-radius: 8px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .total-update {
            animation: pulse 0.5s ease;
        }

        #checkout-btn {
            transition: all 0.3s ease;
        }
        #checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .cart-login-status {
            padding: 0.75rem;
            margin: 0 auto 1.5rem;
            border-radius: 8px;
            background-color: #e3f2fd;
            max-width: 800px;
            text-align: center;
            display: block;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .notification-slide-in {
            animation: slideInRight 0.3s forwards;
        }

        .notification-slide-out {
            animation: slideOutRight 0.3s forwards;
        }

        #notification-area {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            width: 300px;
            overflow: hidden;
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
                        <a class="nav-link" href="about_us.php">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Panier</a>
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

<section class="container my-5">
    <h1 class="text-center mb-4">Votre Panier</h1>
    <hr class="mb-4">


    <div id="notification-area" style="position: fixed; top: 20px; right: 20px; z-index: 1050; width: 300px;"></div>


    <?php if ($flash): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>" role="alert">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <?php if ($userLoggedIn): ?>
        <div class="cart-login-status alert alert-info">
            <strong>Connecté en tant que:</strong> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Utilisateur'); ?>
            <br>
            <small>Votre panier sera automatiquement sauvegardé dans votre compte.</small>
        </div>
    <?php else: ?>
        <div class="cart-login-status alert alert-warning">
            <strong>Vous n'êtes pas connecté.</strong>
            <br>
            <small>
                <a href="../account-handling/login.php?redirect=<?php echo urlencode('panier.php'); ?>" class="alert-link">Connectez-vous</a>
                pour sauvegarder votre panier et retrouvez-le à votre prochain retour.
            </small>
        </div>
    <?php endif; ?>

    <div id="cart-items-container">


    </div>

    <div id="empty-cart-message" class="empty-cart">
        <h2>Votre panier est vide</h2>
        <p class="message">
            Vous n'avez aucun article ajouté à votre panier pour le moment.<br>
            Parcourez nos produits et ajoutez ceux qui vous intéressent !
        </p>
        <a href="products.php" class="btn btn-primary">Retourner à la boutique</a>
    </div>

    <div id="cart-summary" class="row mt-4" style="display:none;">
        <div class="col-md-8"></div>
        <div class="col-md-4">
            <div class="cart-summary">
                <div class="d-flex justify-content-between">
                    <span>Sous-total:</span>
                    <span id="subtotal">€0.00</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>TVA (20%):</span>
                    <span id="tax">€0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total:</span>
                    <span id="total">€0.00</span>
                </div>
                <button id="checkout-btn" class="btn btn-primary w-100 mt-3">
                    Procéder au paiement
                </button>
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

<!-- JS  -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const userLoggedIn = <?php echo $userLoggedIn ? 'true' : 'false'; ?>;
    const userId = <?php echo $userId ? $userId : 'null'; ?>;
    let cartNeedsSaving = false;
    let cartSaveTimeout;

    document.addEventListener('DOMContentLoaded', function() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        <?php if ($userLoggedIn && isset($savedCart) && !empty($savedCart)): ?>
        const savedCart = <?php echo json_encode($savedCart); ?>;

        // utilisation du panier sauvegardé ?
        if (cart.length > 0 && savedCart.length > 0) {
            if (confirm('Nous avons trouvé un panier sauvegardé dans votre compte. Voulez-vous l\'utiliser? (Cliquez sur Annuler pour garder votre panier actuel)')) {
                cart = savedCart;
                localStorage.setItem('cart', JSON.stringify(cart));
                showNotification('Panier restauré depuis votre compte');
            }
        } else if (savedCart.length > 0) {
            cart = savedCart;
            localStorage.setItem('cart', JSON.stringify(cart));
            showNotification('Panier restauré depuis votre compte');
        }
        <?php endif; ?>

        updateCartDisplay(cart);

        document.getElementById('cart-items-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                const productId = e.target.dataset.id;
                removeFromCart(productId);
            } else if (e.target.classList.contains('quantity-decrease')) {
                const productId = e.target.dataset.id;
                updateQuantity(productId, -1);
            } else if (e.target.classList.contains('quantity-increase')) {
                const productId = e.target.dataset.id;
                updateQuantity(productId, 1);
            }
        });

        document.getElementById('checkout-btn').addEventListener('click', function() {
            if (!userLoggedIn) {
                window.location.href = '../account-handling/login.php?redirect=panier.php';
                return;
            }

            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart.length === 0) {
                showNotification('Votre panier est vide', 'warning');
                return;
            }

            // enlever bouton pendant chargement
            const checkoutBtn = this;
            checkoutBtn.disabled = true;
            checkoutBtn.innerHTML = 'Traitement en cours...';

            fetch('panier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'checkout',
                    'cart_data': JSON.stringify(cart)
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // effacer le panier du local storage
                        localStorage.setItem('cart', JSON.stringify([]));

                        // créer notification de succès
                        showNotification('Votre commande a été créée avec succès!', 'success');

                        // redirection vers la page de confirmation de commande
                        setTimeout(() => {
                            window.location.href = `order_confirmation.php?order_id=${data.order_id}`;
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Une erreur s\'est produite', 'danger');
                        checkoutBtn.disabled = false;
                        checkoutBtn.innerHTML = 'Procéder au paiement';
                    }
                })
                .catch(error => {
                    console.error('Error processing order:', error);
                    showNotification('Une erreur s\'est produite lors du traitement', 'danger');
                    checkoutBtn.disabled = false;
                    checkoutBtn.innerHTML = 'Procéder au paiement';
                });
        });
    });

    function updateCartDisplay(cart) {
        const cartContainer = document.getElementById('cart-items-container');
        const emptyCartMessage = document.getElementById('empty-cart-message');
        const cartSummary = document.getElementById('cart-summary');
        const oldTotal = document.getElementById('total')?.textContent;

        if (cart.length === 0) {
            if (cartContainer.children.length > 0) {
                cartContainer.innerHTML = '';
            }

            emptyCartMessage.style.display = 'block';
            emptyCartMessage.style.opacity = '0';
            setTimeout(() => {
                emptyCartMessage.style.opacity = '1';
            }, 50);

            cartSummary.style.display = 'none';


            if (userLoggedIn) {
                saveCartToDatabase();
            }
            return;
        }

        emptyCartMessage.style.display = 'none';
        cartSummary.style.display = 'flex';

        let html = '<div class="row mb-3 fw-bold d-none d-md-flex">' +
            '<div class="col-md-6">Produit</div>' +
            '<div class="col-md-2">Prix unitaire</div>' +
            '<div class="col-md-2">Quantité</div>' +
            '<div class="col-md-2">Total</div>' +
            '</div>';

        let subtotal = 0;

        cart.forEach(item => {
            const itemTotal = parseFloat(item.price) * item.quantity;
            subtotal += itemTotal;

            html += `
        <div class="row cart-item align-items-center" data-id="${item.id}">
            <div class="col-md-6 d-flex align-items-center">
                <img src="${item.image ? `../images/products/${item.category}/${item.image}` : '../images/default.png'}"
                     alt="${item.name}" class="me-3">
                <div>
                    <h5 class="mb-1">${item.name}</h5>
                    <small class="text-muted">Catégorie: ${item.category}</small>
                    <button class="d-block text-danger small remove-item border-0 bg-transparent p-0 mt-1" data-id="${item.id}">
                        <i class="bi bi-x-circle"></i> Supprimer
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                €${parseFloat(item.price).toFixed(2)}
            </div>
            <div class="col-md-2">
                <div class="quantity-control">
                    <button class="quantity-decrease" data-id="${item.id}">-</button>
                    <input type="text" value="${item.quantity}" readonly>
                    <button class="quantity-increase" data-id="${item.id}">+</button>
                </div>
            </div>
            <div class="col-md-2">
                €${itemTotal.toFixed(2)}
            </div>
        </div>`;
        });

        if (cartContainer.innerHTML !== html) {
            cartContainer.innerHTML = html;

            document.querySelectorAll('.cart-item').forEach(item => {
                item.classList.add('new-item');
            });
        }

        const tax = subtotal * 0.2;
        const total = subtotal + tax;

        document.getElementById('subtotal').textContent = `€${subtotal.toFixed(2)}`;
        document.getElementById('tax').textContent = `€${tax.toFixed(2)}`;

        const totalElement = document.getElementById('total');
        totalElement.textContent = `€${total.toFixed(2)}`;

        if (oldTotal && oldTotal !== totalElement.textContent) {
            totalElement.classList.add('total-update');
            setTimeout(() => {
                totalElement.classList.remove('total-update');
            }, 500);
        }


        if (userLoggedIn) {
            cartNeedsSaving = true;
            scheduleCartSave();
        }
    }

    function removeFromCart(productId) {
        const itemToRemove = document.querySelector(`.cart-item[data-id="${productId}"]`);
        if (itemToRemove) {
            itemToRemove.classList.add('removing');
            setTimeout(() => {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart = cart.filter(item => item.id != productId);
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartDisplay(cart);
            }, 300);
        } else {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart = cart.filter(item => item.id != productId);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay(cart);
        }
    }

    function updateQuantity(productId, change) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const item = cart.find(item => item.id == productId);

        if (item) {
            const quantityInput = document.querySelector(`.cart-item[data-id="${productId}"] input`);
            if (quantityInput) {
                quantityInput.style.backgroundColor = '#fbf8cc';
                setTimeout(() => {
                    quantityInput.style.backgroundColor = '';
                }, 300);
            }

            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(productId);
                return;
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay(cart);
        }
    }

    function scheduleCartSave() {

        if (cartSaveTimeout) {
            clearTimeout(cartSaveTimeout);
        }


        cartSaveTimeout = setTimeout(() => {
            if (cartNeedsSaving && userLoggedIn) {
                saveCartToDatabase();
            }
        }, 2000);
    }

    function saveCartToDatabase() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        cartNeedsSaving = false;

        return fetch('panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'save_cart',
                'cart_data': JSON.stringify(cart)
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Panier sauvegardé', 'success');
                }
                return data;
            });
    }

    function showNotification(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show notification-slide-in`;
        alertDiv.role = 'alert';
        alertDiv.style.marginBottom = '10px';
        alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

        const notificationArea = document.getElementById('notification-area');

        if (notificationArea.children.length > 0) {
            const oldAlert = notificationArea.children[0];
            oldAlert.classList.remove('notification-slide-in');
            oldAlert.classList.add('notification-slide-out');

            setTimeout(() => {
                notificationArea.innerHTML = '';
                notificationArea.appendChild(alertDiv);
            }, 300);
        } else {
            notificationArea.appendChild(alertDiv);
        }

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.classList.remove('notification-slide-in');
                alertDiv.classList.add('notification-slide-out');

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 300);
            }
        }, 3000);
    }
</script>
</body>
</html>