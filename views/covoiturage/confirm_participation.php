<?php // views/covoiturage/confirm_participation.php (contenu uniquement) ?>

<div class="card">
  <h1>Confirmer la participation</h1>

  <?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars((string)$errorMessage) ?>
    </div>
    <br>
  <?php endif; ?>

  <p>
    Vous êtes sur le point de participer au covoiturage
    <strong>#<?= (int)$covoiturage['id_covoiturage'] ?></strong>.
  </p>

  <ul>
    <li><strong>Départ :</strong> <?= htmlspecialchars((string)$covoiturage['lieu_depart']) ?></li>
    <li><strong>Arrivée :</strong> <?= htmlspecialchars((string)$covoiturage['lieu_arrivee']) ?></li>
    <li><strong>Date :</strong> <?= htmlspecialchars((string)$covoiturage['date_depart']) ?> <?= htmlspecialchars((string)$covoiturage['heure_depart']) ?></li>
    <li><strong>Prix :</strong> <?= htmlspecialchars((string)$covoiturage['prix_personne']) ?> crédits</li>
  </ul>

  <p class="muted">
    Votre solde actuel : <strong><?= (int)$creditBalance ?></strong> crédits
  </p>

  <form method="post" action="<?= BASE_URL ?>/covoiturage/participer">
    <input type="hidden" name="id_covoiturage" value="<?= (int)$covoiturage['id_covoiturage'] ?>">

    <button type="submit">Confirmer (payer en crédits)</button>
    <a class="btn" href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$covoiturage['id_covoiturage'] ?>">Annuler</a>
  </form>
</div>
