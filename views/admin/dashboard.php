<?php
// views/admin/dashboard.php
?>

<h1>Espace Administrateur</h1>

<div class="form-actions" style="margin-bottom:16px;">
  <a class="btn btn-secondary" href="<?= BASE_URL ?>/">Accueil</a>
  <a class="btn" href="<?= BASE_URL ?>/admin/employees">Gérer employés</a>
</div>

<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="alert alert-error">
    <strong><?= Security::e($_SESSION['flash_error']) ?></strong>
  </div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="card">
  <h2>Stats</h2>

  <h3>Covoiturages par jour</h3>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr><th>Date</th><th>Nombre</th></tr>
      </thead>
      <tbody>
        <?php foreach ($covoituragesParJour as $jour => $nb): ?>
          <tr>
            <td><?= Security::e($jour) ?></td>
            <td><?= (int)$nb ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h3 style="margin-top:18px;">Crédits gagnés par la plateforme (2 crédits / participation sur trajets VALIDE)</h3>
  <p><strong>Total :</strong> <?= (int)$creditsPlateformeTotal ?></p>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr><th>Date</th><th>Crédits</th></tr>
      </thead>
      <tbody>
        <?php foreach ($creditsPlateformeParJour as $jour => $credits): ?>
          <tr>
            <td><?= Security::e($jour) ?></td>
            <td><?= (int)$credits ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <h2>Suspension de comptes</h2>
  <p>Utilise l’ID utilisateur (ex : depuis phpMyAdmin).</p>

  <form method="post" action="<?= BASE_URL ?>/admin/users/suspend" class="form">
    <div class="form-row">
      <label for="id_suspend">ID utilisateur à suspendre *</label>
      <input id="id_suspend" type="number" name="id_utilisateur" min="1" required>
    </div>
    <div class="form-actions">
      <button class="btn" type="submit">Suspendre</button>
    </div>
  </form>

  <form method="post" action="<?= BASE_URL ?>/admin/users/unsuspend" class="form" style="margin-top:12px;">
    <div class="form-row">
      <label for="id_unsuspend">ID utilisateur à réactiver *</label>
      <input id="id_unsuspend" type="number" name="id_utilisateur" min="1" required>
    </div>
    <div class="form-actions">
      <button class="btn btn-secondary" type="submit">Réactiver</button>
    </div>
  </form>
</div>
