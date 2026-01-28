<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title) ?></title>
</head>
<body>
  <h1>EcoRide</h1>

  <form method="get" action="<?= BASE_URL ?>/covoiturages">
    <label>Départ <input name="depart" required></label><br>
    <label>Arrivée <input name="arrivee" required></label><br>
    <label>Date <input type="date" name="date" required></label><br>

    <label>Prix max (crédits)
      <input type="number" name="prixMax" min="0">
    </label><br>

    <label>
      <input type="checkbox" name="eco" value="1">
      Voyage écologique uniquement (électrique)
    </label><br>

    <button type="submit">Rechercher</button>
  </form>

  <?php if (!empty($_SESSION['user_id'])): ?>
    <p>
      Connecté en tant que
      <strong><?= htmlspecialchars((string)$_SESSION['pseudo']) ?></strong>
    </p>

    <form method="post" action="/logout" style="display:inline;">
      <button type="submit">Déconnexion</button>
    </form>
  <?php else: ?>
    <p>
      <a href="/login">Connexion</a> |
      <a href="/register">Inscription</a>
    </p>
  <?php endif; ?>
  
</body>
</html>
