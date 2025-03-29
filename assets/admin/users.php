<?php
require_once 'auth.php';
require_once '../account-handling/settings.php';

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['delete_user'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
    $stmt->execute([$user_id]);
}

// Handle admin status toggle
if (isset($_POST['toggle_admin'])) {
    $user_id = (int)$_POST['toggle_admin'];
    $stmt = $pdo->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
    $stmt->execute([$user_id]);
}

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_pages = ceil($total_users / $per_page);

$users = $pdo->prepare("
    SELECT id, username, email, is_admin, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$users->execute([$per_page, $offset]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - ReverseH4ck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
<div class="admin-layout">
    <div class="admin-sidebar">
        <h2 class="mb-4">Panel Admin</h2>
        <nav>
            <a href="admin_index.php" class="nav-link active">Dashboard</a>
            <a href="products.php" class="nav-link">Produits</a>
            <a href="orders.php" class="nav-link">Commandes</a>
            <a href="#" class="nav-link">Utilisateurs</a>
            <a href="logout.php" class="nav-link text-danger">Se déconnecter</a>
        </nav>
    </div>

    <main class="admin-main p-4">
        <h1 class="mb-4">Users Management</h1>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Admin Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($user = $users->fetch()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['is_admin'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="toggle_admin" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        Toggle Admin
                                    </button>
                                </form>
                                <?php if (!$user['is_admin']): ?>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="delete_user" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
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
                                    <a class="page-link" href="?page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
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
</body>
</html>