<?php
// views/account/history.php
?>

<h1>Historique</h1>

<?php if (!empty($success)): ?>
  <div class="alert alert-success">
    <strong><?= htmlspecialchars((string)$success) ?></strong>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div class="alert alert-error">
    <strong><?= htmlspecialchars((string)$error) ?></strong>
  </div>
<?php endif; ?>

<?php
  $current = $_GET['statut'] ?? '';
?>

<form method="GET" action="<?= BASE_URL ?>/account/history" style="margin: 12px 0 18px;">
  <label for="statut"><strong>Filtrer par statut :</strong></label>
  <select name="statut" id="statut" onchange="this.form.submit()">
    <option value="" <?= $current === '' ? 'selected' : '' ?>>Tous</option>
    <option value="PLANIFIE" <?= $current === 'PLANIFIE' ? 'selected' : '' ?>>PLANIFIE</option>
    <option value="VALIDE" <?= $current === 'VALIDE' ? 'selected' : '' ?>>VALIDE</option>
    <option value="ANNULE" <?= $current === 'ANNULE' ? 'selected' : '' ?>>ANNULE</option>
    <option value="TERMINE" <?= $current === 'TERMINE' ? 'selected' : '' ?>>TERMINE</option>
    <option value="INCIDENT" <?= $current === 'INCIDENT' ? 'selected' : '' ?>>INCIDENT</option>
  </select>

  <noscript>
    <button class="btn btn-secondary" type="submit">Filtrer</button>
  </noscript>
</form>

<div class="card">
  <h2>Mes participations (passager)</h2>

  <?php if (empty($tripsPassenger)): ?>
    <p>Aucune participation.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Trajet</th>
            <th>Départ</th>
            <th>Prix</th>
            <th>Places</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tripsPassenger as $t): ?>
            <tr>
              <td><?= (int)$t['id_covoiturage'] ?></td>
              <td><?= htmlspecialchars((string)$t['lieu_depart']) ?> → <?= htmlspecialchars((string)$t['lieu_arrivee']) ?></td>
              <td><?= htmlspecialchars((string)$t['date_depart']) ?> <?= htmlspecialchars((string)$t['heure_depart']) ?></td>
              <td><?= (int)$t['prix_personne'] ?> crédits</td>
              <td><?= (int)$t['places_prises'] ?>/<?= (int)$t['nb_place'] ?></td>
              <td><?= htmlspecialchars((string)$t['statut']) ?></td>
              <td>
                <a class="btn btn-secondary" href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$t['id_covoiturage'] ?>">Détail</a>

                <form method="post" action="<?= BASE_URL ?>/account/history/cancel-participation" style="display:inline;">
                  <input type="hidden" name="id_covoiturage" value="<?= (int)$t['id_covoiturage'] ?>">
                  <button class="btn" type="submit">Annuler</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h2>Mes covoiturages (chauffeur)</h2>

  <?php if (empty($tripsDriver)): ?>
    <p>Aucun covoiturage créé en tant que chauffeur.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Trajet</th>
            <th>Départ</th>
            <th>Prix</th>
            <th>Places</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tripsDriver as $t): ?>
            <tr>
              <td><?= (int)$t['id_covoiturage'] ?></td>
              <td><?= htmlspecialchars((string)$t['lieu_depart']) ?> → <?= htmlspecialchars((string)$t['lieu_arrivee']) ?></td>
              <td><?= htmlspecialchars((string)$t['date_depart']) ?> <?= htmlspecialchars((string)$t['heure_depart']) ?></td>
              <td><?= (int)$t['prix_personne'] ?> crédits</td>
              <td><?= (int)$t['places_prises'] ?>/<?= (int)$t['nb_place'] ?></td>
              <td><?= htmlspecialchars((string)$t['statut']) ?></td>
              <td>
                <a class="btn btn-secondary" href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$t['id_covoiturage'] ?>">Détail</a>

                <form method="post" action="<?= BASE_URL ?>/account/history/cancel-trip" style="display:inline;">
                  <input type="hidden" name="id_covoiturage" value="<?= (int)$t['id_covoiturage'] ?>">
                  <button class="btn" type="submit">Annuler</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="form-actions">
  <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
</div>
