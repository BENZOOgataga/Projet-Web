<?php
session_start();
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/../utils/notifications.php';

$error = null;
$alreadyLoggedIn = false;
$flash = getFlash();

// Vérification de si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    $error = "Vous êtes déjà connecté. Veuillez d'abord vous déconnecter si vous souhaitez utiliser un autre compte.";
    $alreadyLoggedIn = true;
}

if (!$alreadyLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            if ($user['is_admin']) {
                header('Location: ../admin/admin_index.php');
            } else {
                header("Location: ../../index.php?login=success");
            }
            exit;
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error = "Database connection error.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ReverseH4ck</title>
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
                        <a class="nav-link" href="../website/about_us.php">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../website/contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../website/panier.php">Panier</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mon Compte</a>
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
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>" role="alert">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Login</h2>
                        <form method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="username"
                                        name="username"
                                        required
                                    <?php echo $alreadyLoggedIn ? 'disabled' : ''; ?>
                                >
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        required
                                    <?php echo $alreadyLoggedIn ? 'disabled' : ''; ?>
                                >
                            </div>
                            <button
                                    type="submit"
                                    class="btn btn-primary w-100 mb-3"
                                <?php echo $alreadyLoggedIn ? 'disabled' : ''; ?>
                            >
                                Login
                            </button>
                            <div class="text-center">
                                <p class="mb-0">Pas encore de compte? <a href="register.php">Créez-en un</a></p>
                            </div>
                        </form><br>

                        <?php if ($alreadyLoggedIn): ?>
                            <a href="user_logout.php" class="btn btn-disconnect w-100">
                                Me déconnecter
                            </a>
                        <?php endif; ?>
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