<?php

// views/employe/incidents.php
$e = function ($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
};
?>

<h1>Espace Employé – Trajets en incident</h1>

<?php if (empty($incidents)): ?>
    <p>Aucun trajet en incident pour le moment.</p>
<?php else: ?>

    <table border="1" cellpadding="8" cellspacing="0">
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
                    <div><strong>Commentaire :</strong><br>
                        <?= nl2br($e($row['commentaire'] ?? '')) ?>
                    </div>
                </td>

                <td>
                    <!-- Valider -->
                    <form method="post" action="<?= BASE_URL ?>/employe/incidents/validate" style="margin-bottom:10px;">
                        <input type="hidden" name="id_covoiturage" value="<?= (int)$row['id_covoiturage'] ?>">
                        <button type="submit" onclick="return confirm('Valider ce trajet ? Le chauffeur sera crédité.');">
                            Valider
                        </button>
                    </form>

                    <!-- Refuser -->
                    <form method="post" action="<?= BASE_URL ?>/employe/incidents/refuse">
                        <input type="hidden" name="id_covoiturage" value="<?= (int)$row['id_covoiturage'] ?>">
                        <button type="submit" onclick="return confirm('Refuser ce trajet ? Il sera annulé (pas de crédit).');">
                            Refuser
                        </button>
                    </form>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
