<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Valider mes trajets - EcoRide</title>
</head>
<body>

<nav>
  <a href="<?= BASE_URL ?>/">Accueil</a> |
  <a href="<?= BASE_URL ?>/covoiturages">Covoiturages</a> |
  <a href="<?= BASE_URL ?>/account">Mon compte</a> |
  <a href="<?= BASE_URL ?>/account/history">Historique</a> |
  <a href="<?= BASE_URL ?>/contact">Contact</a> |
  <form method="post" action="<?= BASE_URL ?>/logout" style="display:inline;">
    <button type="submit">Déconnexion</button>
  </form>
</nav>

<hr>

<h1>Valider mes trajets</h1>

<?php if (!empty($success)): ?>
  <p style="color:green;"><strong><?= htmlspecialchars((string)$success) ?></strong></p>
<?php endif; ?>
<?php if (!empty($error)): ?>
  <p style="color:red;"><strong><?= htmlspecialchars((string)$error) ?></strong></p>
<?php endif; ?>

<?php if (empty($tripsToValidate)): ?>
  <p>Aucun trajet à valider.</p>
<?php else: ?>
  <ul>
  <?php foreach ($tripsToValidate as $t): ?>
    <li>
      <strong>#<?= (int)$t['id_covoiturage'] ?></strong>
      — <?= htmlspecialchars((string)$t['lieu_depart']) ?> → <?= htmlspecialchars((string)$t['lieu_arrivee']) ?>
      (<?= htmlspecialchars((string)$t['date_depart']) ?> <?= htmlspecialchars((string)$t['heure_depart']) ?>)
      — <a href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$t['id_covoiturage'] ?>">Détail</a>

      <div style="margin-top:8px; padding:8px; border:1px solid #ccc;">
        <form method="post" action="<?= BASE_URL ?>/account/validate-trips">
          <input type="hidden" name="id_covoiturage" value="<?= (int)$t['id_covoiturage'] ?>">

          <label>Note (1 à 5) :
            <input type="number" name="note" min="1" max="5">
          </label>
          <br><br>

          <label>Commentaire :
            <br>
            <textarea name="commentaire" rows="3" cols="50"></textarea>
          </label>
          <br><br>

          <button type="submit" name="action" value="VALIDE">Valider (trajet OK)</button>
          <button type="submit" name="action" value="INCIDENT">Signaler un incident</button>
        </form>

        <p style="font-size:12px;">
          Incident = commentaire obligatoire. Un incident déclenche un traitement par un employé (US12).
        </p>
      </div>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>

</body>
</html>
