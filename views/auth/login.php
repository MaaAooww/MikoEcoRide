<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Connexion - EcoRide</title>
</head>
<body>
  <h1>Connexion</h1>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><strong><?= htmlspecialchars($error) ?></strong></p>
  <?php endif; ?>

  <form method="post" action="login">
    <label>Email <input type="email" name="email" required></label><br>
    <label>Mot de passe <input type="password" name="password" required></label><br>
    <button type="submit">Se connecter</button>
  </form>

  <p>
    <a href="./">← Accueil</a> |
    <a href="register">Inscription</a>
  </p>
</body>
</html>
