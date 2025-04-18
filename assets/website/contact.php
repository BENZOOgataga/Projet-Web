<?php
session_start();
require_once '../account-handling/settings.php';
require_once '../utils/notifications.php';

if (!isset($_SESSION['user_id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact - ReverseH4ck</title>
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
                            <a class="nav-link" href="products.php">Produits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about_us.php">À propos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="panier.php">Panier</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../account-handling/login.php">Mon Compte</a>
                        </li>
                    </ul>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../../assets/admin/admin_index.php">Admin Dashboard</a>
                        </li>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <section class="py-5 login-required-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="card shadow border-0">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <img src="../images/login.png" alt="Connexion requise" class="img-fluid" style="width: 80px; height: 80px; opacity: 0.8;">
                            </div>
                            <h2 class="mb-3">Connexion requise</h2>
                            <p class="text-muted mb-4">Pour nous contacter et recevoir une assistance personnalisée, veuillez vous connecter à votre compte ou créer un nouveau compte si vous n'en avez pas encore.</p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                <a href="../account-handling/login.php?redirect=<?php echo urlencode('../website/contact.php'); ?>" class="btn btn-primary btn-lg px-4">Se connecter</a>
                                <a href="../account-handling/register.php" class="btn btn-outline-secondary btn-lg px-4">Créer un compte</a>
                            </div>
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
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
# champs requis
    if (empty($_POST['name'] ?? '')) {
        $errors[] = 'Le champ "Votre nom" est requis.';
    }
    if (empty($_POST['email'] ?? '')) {
        $errors[] = 'Le champ "Votre email" est requis.';
    }
    if (empty($_POST['subject'] ?? '')) {
        $errors[] = 'Le champ "Sujet" est requis.';
    }
    if (empty($_POST['message'] ?? '')) {
        $errors[] = 'Le champ "Votre message" est requis.';
    }

    if (count($errors) === 0) {
        $success = true;
        $userId = $_SESSION['user_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
# table contact du formulaire
        $sql = "INSERT INTO contact (user_id, name_contact, email_contact, subject_contact, message_contact)
            VALUES (:user_id, :name_contact, :email_contact, :subject_contact, :message_contact)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':name_contact' => $name,
            ':email_contact' => $email,
            ':subject_contact' => $subject,
            ':message_contact' => $message
        ]);

        $_SESSION['success'] = "Votre message a été envoyé avec succès !";
        header('Location: contact.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - ReverseH4ck</title>
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
                        <a class="nav-link" href="products.php">Produits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.php">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
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
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h2 class="text-center mb-4">Contactez-nous</h2>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="bg-white p-4 rounded shadow-sm h-100">
                        <h3 class="h5 mb-4">Envoyez-nous un message</h3>

                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success">
                                    <?php echo $_SESSION['success']; ?>
                                    <?php unset($_SESSION['success']); // supprime le message après avoir été affiché ?>
                                </div>
                            <?php endif; ?>

                        <form method="POST" action="contact.php">
                            <div class="mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="Votre nom" required>
                            </div>
                            <div class="mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Votre email" required>
                            </div>
                            <div class="mb-3">
                                    <input type="text" name="subject" class="form-control" placeholder="Sujet" required>
                            </div>
                            <div class="mb-3">
                                    <textarea class="form-control" name="message" rows="5" placeholder="Votre message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </form>
                    </div>
                </div>
                    <div class="col-lg-6">
                        <div class="bg-white p-4 rounded shadow-sm h-100">
                            <h3 class="h5 mb-4">Informations de contact</h3>
                            <div class="mb-4">
                                <h4 class="h6 mb-2">Adresse</h4>
                                <p class="mb-0">123 Rue du Commerce</p>
                                <p>75001 Paris, France</p>
                            </div>
                            <div class="mb-4">
                                <h4 class="h6 mb-2">Téléphone</h4>
                                <p>+33 1 23 45 67 89</p>
                            </div>
                            <div class="mb-4">
                                <h4 class="h6 mb-2">Email</h4>
                                <p>contact@ecommerce.com</p>
                            </div>
                            <div>
                                <h4 class="h6 mb-2">Horaires d'ouverture</h4>
                                <p class="mb-0">Lundi - Vendredi: 9h00 - 18h00</p>
                                <p>Samedi: 9h00 - 13h00</p>
                            </div>
                        </div>
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