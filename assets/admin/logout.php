<?php

session_start();

// suppression variables de session
$_SESSION = array();

// destruction cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// destruction session
session_destroy();

// redirection page de connexion
header('Location: ../account-handling/login.php');
exit;