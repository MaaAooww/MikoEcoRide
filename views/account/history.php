<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Historique - EcoRide</title>
</head>
<body>

  <nav>
    <a href="<?= BASE_URL ?>/">Accueil</a> |
    <a href="<?= BASE_URL ?>/covoiturages">Covoiturages</a> |
    <a href="<?= BASE_URL ?>/account">Mon compte</a> |
    <a href="<?= BASE_URL ?>/contact">Contact</a> |
    <form method="post" action="<?= BASE_URL ?>/logout" style="display:inline;">
      <button type="submit">Déconnexion</button>
    </form>
  </nav>

  <hr>

  <h1>Historique</h1>

  <?php if (!empty($success)): ?>
    <p style="color:green;"><strong><?= htmlspecialchars((string)$success) ?></strong></p>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <p style="color:red;"><strong><?= htmlspecialchars((string)$error) ?></strong></p>
  <?php endif; ?>

  <h2>Mes participations (passager)</h2>

  <?php if (empty($tripsPassenger)): ?>
    <p>Aucune participation.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($tripsPassenger as $t): ?>
        <li>
          <strong>#<?= (int)$t['id_covoiturage'] ?></strong>
          — <?= htmlspecialchars((string)$t['lieu_depart']) ?> → <?= htmlspecialchars((string)$t['lieu_arrivee']) ?>
          (<?= htmlspecialchars((string)$t['date_depart']) ?> <?= htmlspecialchars((string)$t['heure_depart']) ?>)
          — Prix: <?= (int)$t['prix_personne'] ?> crédits
          — Statut: <?= htmlspecialchars((string)$t['statut']) ?>
          — <a href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$t['id_covoiturage'] ?>">Détail</a>

          <form method="post" action="<?= BASE_URL ?>/account/history/cancel-participation" style="display:inline;">
            <input type="hidden" name="id_covoiturage" value="<?= (int)$t['id_covoiturage'] ?>">
            <button type="submit">Annuler ma participation</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <hr>

  <h2>Mes covoiturages (chauffeur)</h2>

  <?php if (empty($tripsDriver)): ?>
    <p>Aucun covoiturage créé en tant que chauffeur.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($tripsDriver as $t): ?>
        <li>
          <strong>#<?= (int)$t['id_covoiturage'] ?></strong>
          — <?= htmlspecialchars((string)$t['lieu_depart']) ?> → <?= htmlspecialchars((string)$t['lieu_arrivee']) ?>
          (<?= htmlspecialchars((string)$t['date_depart']) ?> <?= htmlspecialchars((string)$t['heure_depart']) ?>)
          — Prix: <?= (int)$t['prix_personne'] ?> crédits
          — Statut: <?= htmlspecialchars((string)$t['statut']) ?>
          — <a href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$t['id_covoiturage'] ?>">Détail</a>

          <form method="post" action="<?= BASE_URL ?>/account/history/cancel-trip" style="display:inline;">
            <input type="hidden" name="id_covoiturage" value="<?= (int)$t['id_covoiturage'] ?>">
            <button type="submit">Annuler ce covoiturage</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <hr>
  <footer>
    <p>Contact : <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></p>
    <p><a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a></p>
  </footer>

</body>
</html>
