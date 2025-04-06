<?php
// assets/admin/auth.php
session_start();
require_once __DIR__ . '/../account-handling/settings.php';

function isAdmin() {
    // vérification session
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    // vérification statut admin dans la db
    global $pdo;
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user && $user['is_admin'] == 1;
}

// rediriger vers page de connexion si utilisateur pas admin
if (!isAdmin()) {
    header('Location: ../account-handling/login.php');
    exit;
}