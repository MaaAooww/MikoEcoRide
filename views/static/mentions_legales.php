<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mentions légales - EcoRide</title>
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

  <h1>Mentions légales</h1>

  <p><strong>EcoRide</strong> – Plateforme de covoiturage écologique.</p>

  <h2>Éditeur du site</h2>
  <p>EcoRide (startup fictive – projet ECF).</p>

  <h2>Contact</h2>
  <p>Email : <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></p>

  <h2>Hébergement</h2>
  <p>Environnement local XAMPP (projet de formation).</p>

  <hr>

  <footer>
    <p>Contact : <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></p>
  </footer>

</body>
</html>
