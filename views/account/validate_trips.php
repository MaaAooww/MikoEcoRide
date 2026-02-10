<?php
// views/account/validate_trips.php
?>

<h1>Valider mes trajets</h1>

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

<?php if (empty($tripsToValidate)): ?>
  <div class="card">
    <p>Aucun trajet à valider.</p>
    <div class="form-actions">
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
    </div>
  </div>

<?php else: ?>

  <div class="alert">
    <strong>Rappel :</strong> Si tu signales un <strong>incident</strong>, le <strong>commentaire est obligatoire</strong>.
    Un incident sera traité par un employé (US12).
  </div>

  <?php foreach ($tripsToValidate as $t): ?>
    <div class="card">
      <h2>
        #<?= (int)$t['id_covoiturage'] ?>
        — <?= htmlspecialchars((string)$t['lieu_depart']) ?> → <?= htmlspecialchars((string)$t['lieu_arrivee']) ?>
      </h2>

      <p>
        <strong>Départ :</strong>
        <?= htmlspecialchars((string)$t['date_depart']) ?> <?= htmlspecialchars((string)$t['heure_depart']) ?>
        —
        <a href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$t['id_covoiturage'] ?>">Voir le détail</a>
      </p>

      <form method="post" action="<?= BASE_URL ?>/account/validate-trips" class="form" style="margin-top:12px;">
        <input type="hidden" name="id_covoiturage" value="<?= (int)$t['id_covoiturage'] ?>">

        <div class="form-row">
          <label for="note_<?= (int)$t['id_covoiturage'] ?>">Note (1 à 5)</label>
          <input
            id="note_<?= (int)$t['id_covoiturage'] ?>"
            type="number"
            name="note"
            min="1"
            max="5"
            placeholder="Optionnel"
          >
        </div>

        <div class="form-row">
          <label for="commentaire_<?= (int)$t['id_covoiturage'] ?>">Commentaire</label>
          <textarea
            id="commentaire_<?= (int)$t['id_covoiturage'] ?>"
            name="commentaire"
            rows="3"
            placeholder="Obligatoire si incident"
          ></textarea>
        </div>

        <div class="form-actions">
          <button class="btn" type="submit" name="action" value="VALIDE">Valider (trajet OK)</button>
          <button class="btn btn-secondary" type="submit" name="action" value="INCIDENT">Signaler un incident</button>
        </div>
      </form>
    </div>
  <?php endforeach; ?>

  <div class="form-actions">
    <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
  </div>

<?php endif; ?>
