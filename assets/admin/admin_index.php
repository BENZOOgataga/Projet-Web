<?php
require_once 'auth.php';
require_once '../account-handling/settings.php';

$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$products_count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$recent_users = $pdo->query("
    SELECT username, email, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();

$contact_forms = $pdo->query("
    SELECT name_contact, email_contact, subject_contact, message_contact, created_at
    FROM contact
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ReverseH4ck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/admin.css">
</head>

<body>
<div class="admin-layout">
    <div class="admin-sidebar">
        <h2 class="mb-4">
            <a href="../../index.php" style="text-decoration: none; color: inherit;">Panel Admin</a>
        </h2>
        <nav>
            <a href="#" class="nav-link active">Dashboard</a>
            <a href="products.php" class="nav-link">Produits</a>
            <a href="orders.php" class="nav-link">Commandes</a>
            <a href="users.php" class="nav-link">Utilisateurs</a>
            <a href="#" class="nav-link text-warning" id="clear-storage-btn">Vider le cache</a>
            <a href="logout.php" class="nav-link text-danger">Se déconnecter</a>
        </nav>
    </div>

    <main class="admin-main p-4">
        <h1 class="mb-4">Dashboard</h1>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Utilisateurs</h5>
                        <h2 class="card-text"><?php echo $users_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Produits</h5>
                        <h2 class="card-text"><?php echo $products_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Commandes</h5>
                        <h2 class="card-text"><?php echo $orders_count; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Utilisateurs récents</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Date d'inscription</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recent_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Formulaires de contact récents</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Sujet</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($contact_forms as $form): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($form['name_contact']); ?></td>
                            <td><?php echo htmlspecialchars($form['email_contact']); ?></td>
                            <td><?php echo htmlspecialchars($form['subject_contact']); ?></td>
                            <td><?php echo htmlspecialchars($form['message_contact']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($form['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('clear-storage-btn').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir vider le cache local ? Cette action supprimera les données temporaires stockées dans votre navigateur.')) {
            localStorage.clear();
            sessionStorage.clear();
            alert('Cache vidé avec succès !');
        }
    });
</script>
</body>

</html>