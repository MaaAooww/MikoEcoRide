<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>EcoRide</title>
</head>
<body>

  <!-- US2 : Menu minimum -->
  <nav>
    <a href="<?= BASE_URL ?>/">Accueil</a> |
    <a href="<?= BASE_URL ?>/covoiturages">Covoiturages</a> |
    <?php if (isset($_SESSION['user'])): ?>
      | <a href="<?= BASE_URL ?>/account">Mon compte</a>
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

  <!-- US1 : Page d’accueil + barre de recherche -->
  <h1>EcoRide</h1>
  <p>Plateforme de covoiturage écologique (déplacements en voiture uniquement).</p>

  <!-- US1 : images (minimum) -->
  <div>
    <img src="https://picsum.photos/seed/ecoride1/600/200" alt="Covoiturage écologique" style="max-width:100%;height:auto;">
  </div>

  <h2>Rechercher un itinéraire</h2>
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

  <hr>

  <!-- US1 : footer avec mail + mentions légales -->
  <footer>
    <p>Contact : <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></p>
    <p><a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a></p>
  </footer>

</body>
</html>
