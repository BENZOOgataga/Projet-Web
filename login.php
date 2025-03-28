<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/styles/style.css">
  <title>Connexion</title>
</head>

<body>
  <h2>Connexion</h2>
  <form action="login.php" method="post">
    <label for="username">Nom d'utilisateur :</label>
    <input type="text" id="username" name="username" required /><br /><br />

    <label for="password">Mot de passe :</label>
    <input
      type="password"
      id="password"
      name="password"
      required /><br /><br />

    <input type="submit" value="Se connecter" />
  </form>
</body>

<?php

require_once __DIR__ . '/settings.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    header('Location: index.html');
  } else {
    echo "Nom d'utilisateur ou mot de passe incorrect.";
  }
}


?>