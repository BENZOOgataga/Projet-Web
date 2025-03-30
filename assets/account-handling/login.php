<?php
require_once __DIR__ . '/settings.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];

            // On redirige vers la page d'accueil ou le tableau de bord admin selon le statut
            if ($user['is_admin']) {
                header('Location: ../admin/admin_index.php');
            } else {
                header('Location: ../website/products.php');
            }
            exit;
        } else {
            echo "Nom d'utilisateur ou mot de passe incorrect";
        }
    } catch (PDOException $e) {
        echo "Erreur de connexion à la base de données";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="../../assets/images/logo.png" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="../../index.php">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../website/products.php">Produits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../assets/website/about_us.html">À propos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../assets/website/contact.html">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../assets/website/panier.html">Panier</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Mon Compte</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="text-center mb-4">Connexion</h2>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <form method="post" novalidate>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>
                                <div class="text-center">
                                    <p class="mb-0">Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
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