<?php
require_once 'assets/account-handling/settings.php';

function getLatestProducts($pdo, $category, $limit = 4) {
    $stmt = $pdo->prepare("
        SELECT id, name, description, price, original_price, image_url, is_promo, category
        FROM articles 
        WHERE category = ?
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$category, $limit]);
    return $stmt->fetchAll();
}

$phones = getLatestProducts($pdo, 'phones');
$laptops = getLatestProducts($pdo, 'laptops');
$accessories = getLatestProducts($pdo, 'accessories');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ReverseH4ck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="stylesheet" href="assets/styles/style.css">
</head>

<body>
  <header class="header">
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand" href="#">
          <img src="assets/images/logo.png" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#">Accueil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="assets/website/products.php">Produits</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="assets/website/about_us.html">À propos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="assets/website/contact.php">Contact</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="assets/website/panier.php">Panier</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="assets/account-handling/login.php">Mon Compte</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
      <div class="notification notification-success show" id="loginNotification">
          Connexion réussie ! Bienvenue sur ReverseH4ck
      </div>

      <script>
          setTimeout(() => {
              const notification = document.getElementById('loginNotification');
              notification.classList.remove('show');
              // Suppression de la notification après 3 secondes
              setTimeout(() => notification.remove(), 300);
          }, 3000);
      </script>
  <?php endif; ?>

  <section class="hero">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="container">
            <div class="row align-items-center">
              <div class="col-md-6">
                <h1>MacBook Air</h1>
                <p>Découvrez la puissance fulgurante, l'autonomie longue durée et le design élégant du MacBook Air M2 — pensé pour les créatifs, les pros et votre quotidien.</p>
              </div>
              <div class="col-md-6">
                <img src="assets/images/banner-products/product-1.png" alt="MacBook Pro" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="container">
            <div class="row align-items-center">
              <div class="col-md-6">
                <h1>Google Home</h1>
                <p>Contrôlez votre maison, écoutez votre musique et posez vos questions avec Google Home — votre assistant vocal intelligent, toujours prêt à vous aider.</p>
              </div>
              <div class="col-md-6">
                <img src="assets/images/banner-products/slider-1.png" alt="Alexa" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="container">
            <div class="row align-items-center">
              <div class="col-md-6">
                <h1>JBL</h1>
                <p>Un son puissant, des basses profondes et un design nomade — la JBL vous accompagne partout pour faire vibrer chaque instant.</p>
              </div>
              <div class="col-md-6">
                <img src="assets/images/banner-products/slider-3.png" alt="JBL" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </section>

  <!-- Smartphones Section -->
  <section id="smartphone" class="section-products">
    <div class="container">
      <div class="section-header">
        <h2>Téléphones</h2>
        <a href="assets/website/products.php?category=phones" class="view-all">Voir tout</a>
      </div>
      <div class="products-grid">
        <?php foreach ($phones as $phone): ?>
          <div class="product-card">
            <?php if ($phone['is_promo']): ?>
              <div class="promo-badge">En Promotion</div>
            <?php endif; ?>
              <img src="<?php echo (!empty($phone['image_url']))
                  ? 'assets/images/products/' . htmlspecialchars('phones') . '/' . htmlspecialchars($phone['image_url'])
                  : 'assets/images/default.png'; ?>"
                   alt="<?php echo htmlspecialchars($phone['name']); ?>">
              <div class="product-info">
              <h3><?php echo htmlspecialchars($phone['name']); ?></h3>
              <p><?php echo htmlspecialchars($phone['description']); ?></p>
              <div class="price">
                <?php if ($phone['original_price']): ?>
                  <span class="original-price">€<?php echo number_format($phone['original_price'], 2); ?></span>
                  <span class="sale-price">€<?php echo number_format($phone['price'], 2); ?></span>
                <?php else: ?>
                  <span class="current-price">€<?php echo number_format($phone['price'], 2); ?></span>
                <?php endif; ?>
              </div>
                      <a href="#" class="btn btn-primary phone-btn"
                         data-product-id="<?php echo $phone['id']; ?>"
                         data-product-name="<?php echo htmlspecialchars($phone['name']); ?>"
                         data-product-price="<?php echo $phone['price']; ?>"
                         data-product-category="<?php echo $phone['category']; ?>"
                         data-product-image="<?php echo $phone['image_url']; ?>">
                          ACHETER
                          <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right"
                               fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd"
                                    d="M10.146 4.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 8l-2.647-2.646a.5.5 0 0 1 0-.708z" />
                              <path fill-rule="evenodd"
                                    d="M2 8a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8z" />
                          </svg>
                      </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Laptops Section -->
  <section id="laptop" class="section-products">
    <div class="container">
      <div class="section-header">
        <h2>Ordinateurs portables</h2>
        <a href="assets/website/products.php?category=laptops" class="view-all">Voir tout</a>
      </div>
      <div class="products-grid">
        <?php foreach ($laptops as $laptop): ?>
          <div class="product-card">
            <?php if ($laptop['is_promo']): ?>
              <div class="promo-badge">En Promotion</div>
            <?php endif; ?>
              <img src="<?php echo (!empty($laptop['image_url']))
                  ? 'assets/images/products/' . htmlspecialchars('laptops') . '/' . htmlspecialchars($laptop['image_url'])
                  : 'assets/images/default.png'; ?>"
                   alt="<?php echo htmlspecialchars($laptop['name']); ?>">
              <div class="product-info">
              <h3><?php echo htmlspecialchars($laptop['name']); ?></h3>
              <p><?php echo htmlspecialchars($laptop['description']); ?></p>
              <div class="price">
                <?php if ($laptop['original_price']): ?>
                  <span class="original-price">€<?php echo number_format($laptop['original_price'], 2); ?></span>
                  <span class="sale-price">€<?php echo number_format($laptop['price'], 2); ?></span>
                <?php else: ?>
                  <span class="current-price">€<?php echo number_format($laptop['price'], 2); ?></span>
                <?php endif; ?>
              </div>
                  <a href="#" class="btn btn-primary phone-btn"
                     data-product-id="<?php echo $laptop['id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($laptop['name']); ?>"
                     data-product-price="<?php echo $laptop['price']; ?>"
                     data-product-category="<?php echo $laptop['category']; ?>"
                     data-product-image="<?php echo $laptop['image_url']; ?>">
                      ACHETER
                      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right"
                           fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                                d="M10.146 4.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 8l-2.647-2.646a.5.5 0 0 1 0-.708z" />
                          <path fill-rule="evenodd"
                                d="M2 8a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8z" />
                      </svg>
                  </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Accessories Section -->
  <section id="accessories" class="section-products">
    <div class="container">
      <div class="section-header">
        <h2>Accessoires</h2>
        <a href="assets/website/products.php?category=accessories" class="view-all">Voir tout</a>
      </div>
      <div class="products-grid">
        <?php foreach ($accessories as $accessory): ?>
          <div class="product-card">
            <?php if ($accessory['is_promo']): ?>
              <div class="promo-badge">En Promotion</div>
            <?php endif; ?>
              <img src="<?php echo (!empty($accessory['image_url']))
                  ? 'assets/images/products/' . htmlspecialchars('accessories') . '/' . htmlspecialchars($accessory['image_url'])
                  : 'assets/images/default.png'; ?>"
                   alt="<?php echo htmlspecialchars($accessory['name']); ?>">
              <div class="product-info">
              <h3><?php echo htmlspecialchars($accessory['name']); ?></h3>
              <p><?php echo htmlspecialchars($accessory['description']); ?></p>
              <div class="price">
                <?php if ($accessory['original_price']): ?>
                  <span class="original-price">€<?php echo number_format($accessory['original_price'], 2); ?></span>
                  <span class="sale-price">€<?php echo number_format($accessory['price'], 2); ?></span>
                <?php else: ?>
                  <span class="current-price">€<?php echo number_format($accessory['price'], 2); ?></span>
                <?php endif; ?>
              </div>
                  <a href="#" class="btn btn-primary phone-btn"
                     data-product-id="<?php echo $accessory['id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($accessory['name']); ?>"
                     data-product-price="<?php echo $accessory['price']; ?>"
                     data-product-category="<?php echo $accessory['category']; ?>"
                     data-product-image="<?php echo $accessory['image_url']; ?>">
                      ACHETER
                      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right"
                           fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                                d="M10.146 4.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 8l-2.647-2.646a.5.5 0 0 1 0-.708z" />
                          <path fill-rule="evenodd"
                                d="M2 8a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8z" />
                      </svg>
                  </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="categories" class="categories-section">
    <div class="container">
      <h2 class="text-center mb-5">Nos Catégories</h2>
      <div class="row g-4">
        <div class="col-md-3">
          <a href="assets/website/products.php?category=phones" class="category-card-link">
            <div class="category-card">
              <img src="assets/images/phone/phone-2.png" alt="Téléphones">
              <div class="category-overlay">
                <h3>Téléphones</h3>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="assets/website/products.php?category=laptops" class="category-card-link">
            <div class="category-card">
              <img src="assets/images/laptop/product-1.png" alt="Ordinateurs">
              <div class="category-overlay">
                <h3>Ordinateurs</h3>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="assets/website/products.php?category=gaming" class="category-card-link">
            <div class="category-card">
              <img src="assets/images/banner-products/product-1.png" alt="Gaming">
              <div class="category-overlay">
                <h3>Gaming</h3>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="assets/website/products.php?category=accessories" class="category-card-link">
            <div class="category-card">
              <img src="assets/images/banner-products/slider-1.png" alt="Accessoires">
              <div class="category-overlay">
                <h3>Accessoires</h3>
              </div>
            </div>
          </a>
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
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const buyButtons = document.querySelectorAll('.phone-btn, .laptop-btn, .gaming-btn, .accessory-btn');

          buyButtons.forEach(button => {
              button.addEventListener('click', function(e) {
                  e.preventDefault();

                  const productId = this.dataset.productId;
                  const productName = this.dataset.productName;
                  const productPrice = this.dataset.productPrice;
                  const productCategory = this.dataset.productCategory;
                  const productImage = this.dataset.productImage;

                  addToCart(productId, productName, productPrice, productCategory, productImage);
              });
          });

          function addToCart(id, name, price, category, image) {
              let cart = JSON.parse(localStorage.getItem('cart')) || [];

              const existingItemIndex = cart.findIndex(item => item.id == id);

              if (existingItemIndex !== -1) {
                  cart[existingItemIndex].quantity += 1;
              } else {
                  cart.push({
                      id: id,
                      name: name,
                      price: price,
                      category: category,
                      image: image,
                      quantity: 1
                  });
              }

              localStorage.setItem('cart', JSON.stringify(cart));

              showNotification(`${name} a été ajouté au panier`, 'success');
          }

          function showNotification(message, type = 'success') {
              let notificationArea = document.getElementById('notification-area');
              if (!notificationArea) {
                  notificationArea = document.createElement('div');
                  notificationArea.id = 'notification-area';
                  notificationArea.style.position = 'fixed';
                  notificationArea.style.top = '20px';
                  notificationArea.style.right = '20px';
                  notificationArea.style.zIndex = '1050';
                  notificationArea.style.width = '300px';
                  document.body.appendChild(notificationArea);
              }

              const alertDiv = document.createElement('div');
              alertDiv.className = `alert alert-${type} alert-dismissible fade show notification-slide-in`;
              alertDiv.role = 'alert';
              alertDiv.style.marginBottom = '10px';
              alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

              const style = document.createElement('style');
              style.innerHTML = `
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
        `;
              document.head.appendChild(style);

              notificationArea.appendChild(alertDiv);

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
      });
  </script>
</body>
</html>