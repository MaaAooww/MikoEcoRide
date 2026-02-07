<?php
// views/admin/dashboard.php
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>EcoRide - Admin</title>
</head>
<body>
    <h1>Espace Administrateur</h1>

    <p>
        <a href="<?= BASE_URL ?>/">Accueil</a> |
        <a href="<?= BASE_URL ?>/admin/employees">Gérer employés</a>
    </p>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <p><strong><?= Security::e($_SESSION['flash_error']) ?></strong></p>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <h2>Stats</h2>

    <h3>Covoiturages par jour</h3>
    <table border="1" cellpadding="6">
        <tr><th>Date</th><th>Nombre</th></tr>
        <?php foreach ($covoituragesParJour as $jour => $nb): ?>
            <tr>
                <td><?= Security::e($jour) ?></td>
                <td><?= (int)$nb ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Crédits gagnés par la plateforme (2 crédits / participation sur trajets VALIDE)</h3>
    <p><strong>Total :</strong> <?= (int)$creditsPlateformeTotal ?></p>

    <table border="1" cellpadding="6">
        <tr><th>Date</th><th>Crédits</th></tr>
        <?php foreach ($creditsPlateformeParJour as $jour => $credits): ?>
            <tr>
                <td><?= Security::e($jour) ?></td>
                <td><?= (int)$credits ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Suspension de comptes</h2>
    <p>Utilise l’ID utilisateur (ex : depuis phpMyAdmin) :</p>

    <form method="post" action="<?= BASE_URL ?>/admin/users/suspend">
        <label>ID utilisateur à suspendre :</label>
        <input type="number" name="id_utilisateur" min="1" required>
        <button type="submit">Suspendre</button>
    </form>

    <form method="post" action="<?= BASE_URL ?>/admin/users/unsuspend" style="margin-top:10px;">
        <label>ID utilisateur à réactiver :</label>
        <input type="number" name="id_utilisateur" min="1" required>
        <button type="submit">Réactiver</button>
    </form>

</body>
</html>
