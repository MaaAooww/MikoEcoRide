<?php // views/account/index.php (contenu uniquement) ?>

<div class="card">
  <h1>Mon compte</h1>

  <h2>Profil</h2>
  <ul>
    <li><strong>Pseudo :</strong> <?= htmlspecialchars((string)($user['pseudo'] ?? '')) ?></li>
    <li><strong>Email :</strong> <?= htmlspecialchars((string)($user['email'] ?? '')) ?></li>
    <li><strong>Nom :</strong> <?= htmlspecialchars((string)($user['nom'] ?? '')) ?></li>
    <li><strong>Prénom :</strong> <?= htmlspecialchars((string)($user['prenom'] ?? '')) ?></li>
  </ul>
</div>

<br>

<div class="card">
  <h2>Crédits</h2>
  <p><strong>Solde :</strong> <?= (int)$solde ?> crédits</p>
</div>

<br>

<div class="card">
  <h2>Rôles</h2>
  <p>Rôles actuels : <strong><?= htmlspecialchars(implode(', ', $roles)) ?></strong></p>

  <?php $isDriver = in_array('CHAUFFEUR', $roles, true); ?>

  <h3>Je veux être chauffeur</h3>

  <form method="post" action="<?= BASE_URL ?>/account/role" class="form-grid">
    <label class="checkbox">
      <span>Activer le rôle CHAUFFEUR</span>
      <input type="checkbox" name="is_driver" value="1" <?= $isDriver ? 'checked' : '' ?>>
    </label>

    <button type="submit">Enregistrer</button>
  </form>
</div>

<br>

<div class="card">
  <h2>Accès rapides</h2>

  <?php if ($isDriver): ?>
    <p><a class="btn" href="<?= BASE_URL ?>/account/trips/new">Créer un covoiturage</a></p>
    <p><a class="btn" href="<?= BASE_URL ?>/account/vehicles">Gérer mes véhicules</a></p>
  <?php endif; ?>

  <p><a class="btn" href="<?= BASE_URL ?>/account/validate-trips">Valider mes trajets</a></p>
  <p><a class="btn" href="<?= BASE_URL ?>/account/history">Voir mon historique</a></p>
</div>
