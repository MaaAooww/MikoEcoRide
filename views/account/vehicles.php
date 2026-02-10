<?php
// views/account/vehicles.php
?>

<h1>Mes véhicules</h1>

<?php if (!empty($error)): ?>
  <div class="alert alert-error">
    <strong><?= htmlspecialchars((string)$error) ?></strong>
  </div>
<?php endif; ?>

<div class="card">
  <h2>Liste</h2>

  <?php if (empty($vehicles)): ?>
    <p>Aucun véhicule enregistré.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Immatriculation</th>
            <th>Énergie</th>
            <th>Couleur</th>
            <th>Date 1ère immat.</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vehicles as $v): ?>
            <tr>
              <td><?= (int)$v['id_voiture'] ?></td>
              <td><?= htmlspecialchars((string)($v['marque'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string)$v['modele']) ?></td>
              <td><?= htmlspecialchars((string)$v['immatriculation']) ?></td>
              <td><?= htmlspecialchars((string)$v['energie']) ?></td>
              <td><?= htmlspecialchars((string)$v['couleur']) ?></td>
              <td><?= htmlspecialchars((string)$v['date_premiere_immatriculation']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h2>Ajouter un véhicule</h2>

  <form method="post" action="<?= BASE_URL ?>/account/vehicles" class="form">
    <div class="form-row">
      <label for="id_marque">Marque (optionnel)</label>
      <select id="id_marque" name="id_marque">
        <option value="0">-- (optionnel) --</option>
        <?php foreach ($marques as $m): ?>
          <option value="<?= (int)$m['id_marque'] ?>">
            <?= htmlspecialchars((string)$m['libelle']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-row">
      <label for="modele">Modèle *</label>
      <input id="modele" name="modele" required>
    </div>

    <div class="form-row">
      <label for="immatriculation">Immatriculation *</label>
      <input id="immatriculation" name="immatriculation" required>
    </div>

    <div class="form-row">
      <label for="energie">Énergie *</label>
      <input id="energie" name="energie" required placeholder="electrique, essence, diesel...">
    </div>

    <div class="form-row">
      <label for="couleur">Couleur</label>
      <input id="couleur" name="couleur">
    </div>

    <div class="form-row">
      <label for="date_premiere_immatriculation">Date 1ère immat.</label>
      <input id="date_premiere_immatriculation" name="date_premiere_immatriculation" placeholder="YYYY-MM-DD">
    </div>

    <div class="form-actions">
      <button class="btn" type="submit">Ajouter</button>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
    </div>
  </form>
</div>
