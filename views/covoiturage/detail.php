<?php // views/covoiturage/detail.php (contenu uniquement) ?>

<?php $statut = (string)($covoit['statut'] ?? ''); ?>

<div class="card">
  <h1>Détail du covoiturage #<?= (int)$covoit['id_covoiturage'] ?></h1>

  <p>
    <a href="<?= BASE_URL ?>/covoiturages">← Retour liste</a>
  </p>

  <div class="badge">Statut : <strong><?= htmlspecialchars($statut) ?></strong></div>

  <br>

  <ul>
    <li><strong>Départ :</strong> <?= htmlspecialchars((string)$covoit['lieu_depart']) ?>
      (<?= htmlspecialchars((string)$covoit['date_depart']) ?> <?= htmlspecialchars((string)$covoit['heure_depart']) ?>)
    </li>

    <li><strong>Arrivée :</strong> <?= htmlspecialchars((string)$covoit['lieu_arrivee']) ?>
      (<?= htmlspecialchars((string)$covoit['date_arrivee']) ?> <?= htmlspecialchars((string)$covoit['heure_arrivee']) ?>)
    </li>

    <li>
      <strong>Places :</strong>
      <?= (int)$covoit['nb_place'] ?>
      (prises : <?= (int)$covoit['places_prises'] ?>,
      restantes : <?= (int)$covoit['places_restantes'] ?>)
    </li>
    
    <li>
      <strong>Prix :</strong> <?= htmlspecialchars((string)$covoit['prix_personne']) ?> crédits
    </li>

    <?php
      $chauffeurPseudo = (string)($covoit['chauffeur_pseudo'] ?? '');
      $note = $covoit['chauffeur_note_moyenne'] ?? null;
      $nbAvis = (int)($covoit['chauffeur_nb_avis'] ?? 0);
    ?>

    <li><strong>Chauffeur :</strong> <?= htmlspecialchars($chauffeurPseudo !== '' ? $chauffeurPseudo : '—') ?></li>

    <li>
      <strong>Note chauffeur :</strong>
      <?php if ($note === null): ?>
        —
      <?php else: ?>
        <?= number_format((float)$note, 1, ',', ' ') ?>/5 (<?= $nbAvis ?> avis)
      <?php endif; ?>
    </li>
  </ul>
</div>

<br>

<div class="card">
  <?php if (!empty($isDriverOfTrip)): ?>

    <h2>Actions chauffeur</h2>

    <?php if ($statut === 'PLANIFIE'): ?>
      <form method="post" action="<?= BASE_URL ?>/covoiturage/start">
        <input type="hidden" name="id_covoiturage" value="<?= (int)$covoit['id_covoiturage'] ?>">
        <button type="submit">Démarrer le covoiturage</button>
      </form>

    <?php elseif ($statut === 'EN_COURS'): ?>
      <form method="post" action="<?= BASE_URL ?>/covoiturage/finish">
        <input type="hidden" name="id_covoiturage" value="<?= (int)$covoit['id_covoiturage'] ?>">
        <button type="submit">Terminer le covoiturage</button>
      </form>

    <?php elseif ($statut === 'TERMINE'): ?>
      <p><strong>Trajet terminé.</strong> En attente de validation des passagers.</p>

    <?php elseif ($statut === 'VALIDE'): ?>
      <p><strong>Trajet validé.</strong> Crédits chauffeur mis à jour.</p>

    <?php elseif ($statut === 'INCIDENT'): ?>
      <div class="alert alert-danger">
        <strong>Incident signalé.</strong> Un employé doit traiter la situation (US12).
      </div>
    <?php endif; ?>

  <?php else: ?>

    <h2>Participation</h2>

    <?php if (isset($_SESSION['user'])): ?>

      <?php if ($statut === 'PLANIFIE'): ?>
        <p>
          <a class="btn" href="<?= BASE_URL ?>/covoiturage/participer?id=<?= (int)$covoit['id_covoiturage'] ?>">
            Participer (<?= (int)$covoit['prix_personne'] ?> crédits)
          </a>
        </p>
      <?php else: ?>
        <p class="muted">
          Ce covoiturage n’est pas disponible à la participation (statut : <?= htmlspecialchars($statut) ?>).
        </p>
      <?php endif; ?>

    <?php else: ?>

      <p>
        <a class="btn" href="<?= BASE_URL ?>/login">Se connecter pour participer</a>
      </p>

    <?php endif; ?>

  <?php endif; ?>
</div>

<br>

<div class="card">
  <h2>Véhicule</h2>

  <?php if (!empty($covoit['modele'])): ?>
    <ul>
      <li><strong>Modèle :</strong> <?= htmlspecialchars((string)$covoit['modele']) ?></li>
      <li><strong>Énergie :</strong> <?= htmlspecialchars((string)$covoit['energie']) ?></li>
      <li><strong>Couleur :</strong> <?= htmlspecialchars((string)$covoit['couleur']) ?></li>
      <li><strong>Immatriculation :</strong> <?= htmlspecialchars((string)$covoit['immatriculation']) ?></li>
    </ul>
  <?php else: ?>
    <p class="muted">Aucun véhicule associé à ce trajet.</p>
  <?php endif; ?>
</div>
