<?php

session_start();

// Suppression des variables de session
$_SESSION = array();

// Destruction du cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruction de la session
session_destroy();

// Redirection vers la page de connexion
header('Location: ../account-handling/login.php');
exit;