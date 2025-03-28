<title>Register</title>
<link rel="stylesheet" href="/assets/styles/style.css">

<form method="post">
  <input type="text" name="username" placeholder="Nom d'utilisateur" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Mot de passe" required>
  <button type="submit">S'inscrire</button>
</form>

<?php

require_once __DIR__ . '/settings.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $user_exist = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
  $user_exist->execute([$username]);

  if ($user_exist->rowCount() > 0) {
    echo "Cet utilisateur existe déjà.";
  } else {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password]);

    echo "Inscription réussie.";
  }
}
?>