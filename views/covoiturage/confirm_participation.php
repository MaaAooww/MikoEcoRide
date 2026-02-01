<?php
// views/covoiturage/confirm_participation.php
?>

<h1>Confirmer votre participation</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<p>
    Vous allez utiliser <strong><?= (int)$prixCredits ?> crédits</strong>
    pour participer à ce covoiturage.
</p>

<form method="post" action="<?= BASE_URL ?>/covoiturage/participer">
    <input type="hidden" name="id_covoiturage" value="<?= (int)$idCovoiturage ?>">
    <button type="submit" class="btn btn-success">Confirmer</button>
    <a class="btn btn-secondary" href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$idCovoiturage ?>">Annuler</a>
</form>
