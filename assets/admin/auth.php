<?php
// assets/admin/auth.php
session_start();
require_once __DIR__ . '/../account-handling/settings.php';

function isAdmin() {
    // Vérification de la session
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    // Vérification du statut d'admin dans la base de données
    global $pdo;
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user && $user['is_admin'] == 1;
}

// Rediriger vers la page de connexion si l'utilisateur n'est pas admin
if (!isAdmin()) {
    header('Location: ../account-handling/login.php');
    exit;
}