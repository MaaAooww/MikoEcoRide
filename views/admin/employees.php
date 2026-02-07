<?php
// views/admin/employees.php
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>EcoRide - Admin - Employés</title>
</head>
<body>
    <h1>Gestion des employés</h1>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <p><strong><?= Security::e($_SESSION['flash_error']) ?></strong></p>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <p>
        <a href="<?= BASE_URL ?>/admin">⬅ Retour dashboard</a>
    </p>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <p><strong><?= Security::e($_SESSION['flash_error']) ?></strong></p>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <h2>Liste des employés</h2>
    <table border="1" cellpadding="6">
        <tr><th>ID</th><th>Pseudo</th><th>Email</th></tr>
        <?php foreach ($employes as $e): ?>
            <tr>
                <td><?= (int)$e['id_utilisateur'] ?></td>
                <td><?= Security::e($e['pseudo']) ?></td>
                <td><?= Security::e($e['email']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Créer un employé</h2>
    <form method="post" action="<?= BASE_URL ?>/admin/employees/create">
        <div>
            <label>Pseudo</label><br>
            <input type="text" name="pseudo" required>
        </div>
        <div>
            <label>Email</label><br>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Mot de passe</label><br>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Créer</button>
    </form>
</body>
</html>
