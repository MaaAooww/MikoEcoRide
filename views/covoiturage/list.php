<?php // views/covoiturage/list.php (contenu uniquement) ?>

<div class="card">
  <h1>Résultats de recherche</h1>

  <p class="muted">
    Filtres :
    départ=<strong><?= htmlspecialchars((string)($depart ?? '')) ?></strong>,
    arrivée=<strong><?= htmlspecialchars((string)($arrivee ?? '')) ?></strong>,
    date=<strong><?= htmlspecialchars((string)($date ?? '')) ?></strong>,
    prixMax=<strong><?= htmlspecialchars((string)($prixMax ?? '')) ?></strong>,
    eco=<strong><?= ($ecoOnly ? 'Oui' : 'Non') ?></strong>
  </p>

  <p>
    <a href="<?= BASE_URL ?>/">← Nouvelle recherche</a>
  </p>
</div>

<br>

<?php if (empty($results)): ?>
  <div class="card">
    <p><strong>Aucun covoiturage trouvé.</strong></p>
    <p class="muted">Astuce : change la date ou teste Lyon → Paris le 2026-02-01 (données de test).</p>
  </div>
<?php else: ?>
  <table class="table">
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
      <?php $eco = isset($row['energie']) && stripos((string)$row['energie'], 'elect') !== false; ?>
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
          <a href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$row['id_covoiturage'] ?>">Détail</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
