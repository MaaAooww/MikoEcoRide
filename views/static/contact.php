<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Contact - EcoRide</title>
</head>
<body>

  <nav>
    <a href="<?= BASE_URL ?>/">Accueil</a> |
    <a href="<?= BASE_URL ?>/covoiturages">Covoiturages</a> |
    <?php if (isset($_SESSION['user'])): ?>
      <span>Connecté : <strong><?= htmlspecialchars((string)$_SESSION['user']['pseudo']) ?></strong></span> |
      <form method="post" action="<?= BASE_URL ?>/logout" style="display:inline;">
        <button type="submit">Déconnexion</button>
      </form>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/login">Connexion</a> |
      <a href="<?= BASE_URL ?>/register">Inscription</a>
    <?php endif; ?>
    | <a href="<?= BASE_URL ?>/contact">Contact</a>
  </nav>

  <hr>

  <h1>Contact</h1>
  <p>Pour toute question :</p>
  <ul>
    <li>Email : <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></li>
  </ul>

  <hr>

  <footer>
    <p><a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a></p>
  </footer>

</body>
</html>
