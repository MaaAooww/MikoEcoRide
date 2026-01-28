<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Résultats - EcoRide</title>
</head>
<body>
  <h1>Résultats de recherche</h1>

  <p>
    Filtres :
    départ=<strong><?= htmlspecialchars((string)($depart ?? '')) ?></strong>,
    arrivée=<strong><?= htmlspecialchars((string)($arrivee ?? '')) ?></strong>,
    date=<strong><?= htmlspecialchars((string)($date ?? '')) ?></strong>
    prixMax=<strong><?= htmlspecialchars((string)($prixMax ?? '')) ?></strong>,
    eco=<strong><?= ($ecoOnly ? 'Oui' : 'Non') ?></strong>
  </p>

  <p>
    <a href="/">← Nouvelle recherche</a>
  </p>

  <?php if (empty($results)): ?>
    <p><strong>Aucun covoiturage trouvé.</strong></p>
    <p>Astuce : change la date ou teste Lyon → Paris le 2026-02-01 (données de test).</p>
  <?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>Départ</th>
          <th>Arrivée</th>
          <th>Date / Heure</th>
          <th>Places</th>
          <th>Prix</th>
          <th>Éco</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($results as $row): ?>
        <?php
          $eco = isset($row['energie']) && stripos((string)$row['energie'], 'elect') !== false;
        ?>
        <tr>
          <td><?= htmlspecialchars((string)$row['lieu_depart']) ?></td>
          <td><?= htmlspecialchars((string)$row['lieu_arrivee']) ?></td>
          <td>
            <?= htmlspecialchars((string)$row['date_depart']) ?>
            <?= htmlspecialchars((string)$row['heure_depart']) ?>
          </td>
          <td><?= htmlspecialchars((string)$row['nb_place']) ?></td>
          <td><?= htmlspecialchars((string)$row['prix_personne']) ?> crédits</td>
          <td><?= $eco ? 'Oui' : 'Non' ?></td>
          <td>
            <a href="/covoiturage?id=<?= (int)$row['id_covoiturage'] ?>">Détail</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
