<?php
// views/account/new_trip.php
?>

<h1>Créer un covoiturage</h1>

<div class="alert">
  <strong>Info :</strong> La plateforme prélève <strong>2 crédits</strong> par participation. Fixe ton prix en conséquence.
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-error">
    <strong><?= htmlspecialchars((string)$error) ?></strong>
  </div>
<?php endif; ?>

<?php if (empty($vehicles)): ?>
  <div class="alert alert-error">
    Tu dois d’abord <strong>ajouter un véhicule</strong> pour créer un covoiturage.
    <div style="margin-top:10px;">
      <a class="btn" href="<?= BASE_URL ?>/account/vehicles">Ajouter un véhicule</a>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
    </div>
  </div>
<?php else: ?>

  <form method="post" action="<?= BASE_URL ?>/account/trips/new" class="card form">

    <h2>Trajet</h2>

    <div class="form-row">
      <label for="lieu_depart">Lieu départ *</label>
      <input id="lieu_depart" name="lieu_depart" required>
    </div>

    <div class="form-row">
      <label for="date_depart">Date départ *</label>
      <input id="date_depart" type="date" name="date_depart" required>
    </div>

    <div class="form-row">
      <label for="heure_depart">Heure départ *</label>
      <input id="heure_depart" type="time" name="heure_depart" required>
    </div>

    <div class="form-row">
      <label for="lieu_arrivee">Lieu arrivée *</label>
      <input id="lieu_arrivee" name="lieu_arrivee" required>
    </div>

    <div class="form-row">
      <label for="date_arrivee">Date arrivée</label>
      <input id="date_arrivee" type="date" name="date_arrivee">
    </div>

    <div class="form-row">
      <label for="heure_arrivee">Heure arrivée</label>
      <input id="heure_arrivee" type="time" name="heure_arrivee">
    </div>

    <hr>

    <h2>Offre</h2>

    <div class="form-row">
      <label for="nb_place">Places *</label>
      <input id="nb_place" type="number" name="nb_place" required min="1">
    </div>

    <div class="form-row">
      <label for="prix_personne">Prix par personne (crédits)</label>
      <input id="prix_personne" type="number" name="prix_personne" min="0">
    </div>

    <div class="form-row">
      <label for="id_voiture">Véhicule *</label>
      <select id="id_voiture" name="id_voiture" required>
        <?php foreach ($vehicles as $v): ?>
          <option value="<?= (int)$v['id_voiture'] ?>">
            #<?= (int)$v['id_voiture'] ?> - <?= htmlspecialchars((string)($v['marque'] ?? '')) ?>
            <?= htmlspecialchars((string)$v['modele']) ?> (<?= htmlspecialchars((string)$v['energie']) ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <small>
        Besoin d’un autre véhicule ?
        <a href="<?= BASE_URL ?>/account/vehicles">Ajouter un véhicule</a>
      </small>
    </div>

    <div class="form-actions">
      <button class="btn" type="submit">Créer</button>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/account">Retour</a>
    </div>

  </form>

<?php endif; ?>
