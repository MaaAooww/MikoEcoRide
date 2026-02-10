<?php
// views/employe/incidents.php
$e = function ($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
};
?>

<h1>Espace Employé – Trajets en incident</h1>

<?php if (empty($incidents)): ?>
  <div class="card">
    <p>Aucun trajet en incident pour le moment.</p>
    <div class="form-actions">
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
    </div>
  </div>
<?php else: ?>

  <div class="alert">
    <strong>Rappel :</strong> “Valider” crédite le chauffeur. “Refuser” annule le trajet (pas de crédit).
  </div>

  <div class="card">
    <h2>Liste des incidents</h2>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Trajet</th>
            <th>Chauffeur</th>
            <th>Passager</th>
            <th>Avis (incident)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($incidents as $row): ?>
          <tr>
            <td><?= (int)$row['id_covoiturage'] ?></td>

            <td>
              <div>
                <strong>Départ :</strong>
                <?= $e($row['lieu_depart'] ?? '') ?>
                le <?= $e($row['date_depart'] ?? '') ?>
              </div>
              <div>
                <strong>Arrivée :</strong>
                <?= $e($row['lieu_arrivee'] ?? '') ?>
                le <?= $e($row['date_arrivee'] ?? '') ?>
              </div>
              <div>
                <strong>Statut :</strong> <?= $e($row['statut'] ?? '') ?>
              </div>
              <div>
                <strong>Prix / pers :</strong> <?= (int)($row['prix_personne'] ?? 0) ?> crédits
              </div>
            </td>

            <td>
              <div><strong><?= $e($row['chauffeur_pseudo'] ?? '') ?></strong></div>
              <div><?= $e($row['chauffeur_email'] ?? '') ?></div>
            </td>

            <td>
              <div><strong><?= $e($row['passager_pseudo'] ?? '') ?></strong></div>
              <div><?= $e($row['passager_email'] ?? '') ?></div>
            </td>

            <td>
              <div><strong>Note :</strong> <?= (int)($row['note'] ?? 0) ?>/5</div>
              <div style="margin-top:6px;">
                <strong>Commentaire :</strong><br>
                <?= nl2br($e($row['commentaire'] ?? '')) ?>
              </div>
            </td>

            <td>
              <div class="form-actions">
                <form method="post" action="<?= BASE_URL ?>/employe/incidents/validate" style="display:inline;">
                  <input type="hidden" name="id_covoiturage" value="<?= (int)$row['id_covoiturage'] ?>">
                  <button class="btn" type="submit"
                          onclick="return confirm('Valider ce trajet ? Le chauffeur sera crédité.');">
                    Valider
                  </button>
                </form>

                <form method="post" action="<?= BASE_URL ?>/employe/incidents/refuse" style="display:inline;">
                  <input type="hidden" name="id_covoiturage" value="<?= (int)$row['id_covoiturage'] ?>">
                  <button class="btn btn-secondary" type="submit"
                          onclick="return confirm('Refuser ce trajet ? Il sera annulé (pas de crédit).');">
                    Refuser
                  </button>
                </form>
              </div>
            </td>

          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="form-actions">
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
    </div>
  </div>

<?php endif; ?>
