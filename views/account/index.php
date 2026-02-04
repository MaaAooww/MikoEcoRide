<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mon compte - EcoRide</title>
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

  <h1>Mon compte</h1>

  <h2>Profil</h2>
  <ul>
    <li><strong>Pseudo :</strong> <?= htmlspecialchars((string)($user['pseudo'] ?? '')) ?></li>
    <li><strong>Email :</strong> <?= htmlspecialchars((string)($user['email'] ?? '')) ?></li>
    <li><strong>Nom :</strong> <?= htmlspecialchars((string)($user['nom'] ?? '')) ?></li>
    <li><strong>Prénom :</strong> <?= htmlspecialchars((string)($user['prenom'] ?? '')) ?></li>
  </ul>

  <h2>Crédits</h2>
  <p><strong>Solde :</strong> <?= (int)$solde ?> crédits</p>

  <h2>Rôles</h2>
  <p>Rôles actuels : <strong><?= htmlspecialchars(implode(', ', $roles)) ?></strong></p>

  <?php
    $isDriver = in_array('CHAUFFEUR', $roles, true);
  ?>

  <h3>Je veux être chauffeur</h3>
  <form method="post" action="<?= BASE_URL ?>/account/role">
    <label>
      <input type="checkbox" name="is_driver" value="1" <?= $isDriver ? 'checked' : '' ?>>
      Activer le rôle CHAUFFEUR
    </label>
    <button type="submit">Enregistrer</button>
  </form>

  <?php if ($isDriver): ?>
    <hr>
    <h2>Chauffeur</h2>
    <p><a href="<?= BASE_URL ?>/account/trips/new">Créer un covoiturage</a></p>
    <p><a href="<?= BASE_URL ?>/account/vehicles">Gérer mes véhicules</a></p>
  <?php endif; ?>

  <p><a href="<?= BASE_URL ?>/account/history">Voir mon historique</a></p>

  <hr>

  <footer>
    <p>Contact : <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></p>
    <p><a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a></p>
  </footer>

</body>
</html>
