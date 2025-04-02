<!-- register.php -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/styles/style.css">
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
                            <a class="nav-link" href="../website/contact.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../website/panier.html">Panier</a>
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
                            <h2 class="text-center mb-4">Inscription</h2>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            <form method="post" novalidate>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">J'accepte les conditions d'utilisation</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mb-3">S'inscrire</button>
                                <div class="text-center">
                                    <p class="mb-0">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
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

<?php
require_once __DIR__ . '/settings.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        echo "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Format d'email invalide.";
    } elseif ($password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas.";
    } elseif (!isset($_POST['terms'])) {
        echo "Vous devez accepter les conditions d'utilisation.";
    } else {
        $user_exist = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $user_exist->execute([$username, $email]);

        if ($user_exist->rowCount() > 0) {
            echo "Ce nom d'utilisateur ou cet email existe déjà.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);

            echo "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }
}
?>