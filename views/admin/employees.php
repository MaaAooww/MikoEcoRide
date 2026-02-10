<?php
// views/admin/employees.php
?>

<h1>Gestion des employés</h1>

<div class="form-actions" style="margin-bottom:16px;">
  <a class="btn btn-secondary" href="<?= BASE_URL ?>/admin">⬅ Retour dashboard</a>
</div>

<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="alert alert-error">
    <strong><?= Security::e($_SESSION['flash_error']) ?></strong>
  </div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="card">
  <h2>Liste des employés</h2>

  <?php if (empty($employes)): ?>
    <p>Aucun employé.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr><th>ID</th><th>Pseudo</th><th>Email</th></tr>
        </thead>
        <tbody>
          <?php foreach ($employes as $e): ?>
            <tr>
              <td><?= (int)$e['id_utilisateur'] ?></td>
              <td><?= Security::e($e['pseudo']) ?></td>
              <td><?= Security::e($e['email']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h2>Créer un employé</h2>

  <form method="post" action="<?= BASE_URL ?>/admin/employees/create" class="form">
    <div class="form-row">
      <label for="pseudo">Pseudo *</label>
      <input id="pseudo" type="text" name="pseudo" required>
    </div>

    <div class="form-row">
      <label for="email">Email *</label>
      <input id="email" type="email" name="email" required>
    </div>

    <div class="form-row">
      <label for="password">Mot de passe *</label>
      <input id="password" type="password" name="password" required>
    </div>

    <div class="form-actions">
      <button class="btn" type="submit">Créer</button>
    </div>
  </form>
</div>
