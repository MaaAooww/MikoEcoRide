<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Détail - EcoRide</title>
</head>
<body>
  <h1>Détail du covoiturage #<?= (int)$covoit['id_covoiturage'] ?></h1>

  <p><a href="<?= BASE_URL ?>/covoiturages?depart=<?= urlencode((string)$covoit['lieu_depart']) ?>&arrivee=<?= urlencode((string)$covoit['lieu_arrivee']) ?>&date=<?= urlencode((string)$covoit['date_depart']) ?>">← Retour liste</a></p>

  <ul>
    <li><strong>Départ :</strong> <?= htmlspecialchars((string)$covoit['lieu_depart']) ?> (<?= htmlspecialchars((string)$covoit['date_depart']) ?> <?= htmlspecialchars((string)$covoit['heure_depart']) ?>)</li>
    <li><strong>Arrivée :</strong> <?= htmlspecialchars((string)$covoit['lieu_arrivee']) ?> (<?= htmlspecialchars((string)$covoit['date_arrivee']) ?> <?= htmlspecialchars((string)$covoit['heure_arrivee']) ?>)</li>
    <li><strong>Statut :</strong> <?= htmlspecialchars((string)$covoit['statut']) ?></li>
    <li><strong>Places :</strong> <?= htmlspecialchars((string)$covoit['nb_place']) ?></li>
    <li><strong>Prix :</strong> <?= htmlspecialchars((string)$covoit['prix_personne']) ?> crédits</li>
  </ul>

  <?php if (isset($_SESSION['user'])): ?>
    <p>
      <a
        href="<?= BASE_URL ?>/covoiturage/participer?id=<?= (int)$covoit['id_covoiturage'] ?>"
        class="btn btn-success"
      >
        Participer (<?= (int)$covoit['prix_personne'] ?> crédits)
      </a>
    </p>
  <?php else: ?>
    <p>
      <a
        href="<?= BASE_URL ?>/login"
        class="btn btn-primary"
      >
        Se connecter pour participer
      </a>
    </p>
  <?php endif; ?>

  <h2>Véhicule</h2>
  <?php if (!empty($covoit['modele'])): ?>
    <ul>
      <li><strong>Modèle :</strong> <?= htmlspecialchars((string)$covoit['modele']) ?></li>
      <li><strong>Énergie :</strong> <?= htmlspecialchars((string)$covoit['energie']) ?></li>
      <li><strong>Couleur :</strong> <?= htmlspecialchars((string)$covoit['couleur']) ?></li>
      <li><strong>Immatriculation :</strong> <?= htmlspecialchars((string)$covoit['immatriculation']) ?></li>
    </ul>
  <?php else: ?>
    <p>Aucun véhicule associé (table `utilise` vide ou non renseignée pour ce trajet).</p>
  <?php endif; ?>
</body>
</html>
