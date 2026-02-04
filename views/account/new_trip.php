<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Créer un covoiturage - EcoRide</title>
</head>
<body>

  <nav>
    <a href="<?= BASE_URL ?>/">Accueil</a> |
    <a href="<?= BASE_URL ?>/covoiturages">Covoiturages</a> |
    <a href="<?= BASE_URL ?>/account">Mon compte</a> |
    <a href="<?= BASE_URL ?>/account/vehicles">Mes véhicules</a> |
    <a href="<?= BASE_URL ?>/contact">Contact</a> |
    <form method="post" action="<?= BASE_URL ?>/logout" style="display:inline;">
      <button type="submit">Déconnexion</button>
    </form>
  </nav>

  <hr>

  <h1>Créer un covoiturage</h1>

  <p><strong>Info :</strong> La plateforme prélève <strong>2 crédits</strong> par participation. Fixe ton prix en conséquence.</p>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><strong><?= htmlspecialchars((string)$error) ?></strong></p>
  <?php endif; ?>

  <?php if (empty($vehicles)): ?>
    <p style="color:red;">
      Tu dois d’abord <strong>ajouter un véhicule</strong> pour créer un covoiturage.
      <a href="<?= BASE_URL ?>/account/vehicles">Ajouter un véhicule</a>
    </p>
  <?php else: ?>

    <form method="post" action="<?= BASE_URL ?>/account/trips/new">
      <fieldset>
        <legend>Trajet</legend>
        <label>Lieu départ* : <input name="lieu_depart" required></label><br><br>
        <label>Date départ* : <input name="date_depart" required placeholder="YYYY-MM-DD"></label><br><br>
        <label>Heure départ* : <input name="heure_depart" required placeholder="HH:MM"></label><br><br>

        <label>Lieu arrivée* : <input name="lieu_arrivee" required></label><br><br>
        <label>Date arrivée : <input name="date_arrivee" placeholder="YYYY-MM-DD"></label><br><br>
        <label>Heure arrivée : <input name="heure_arrivee" placeholder="HH:MM"></label><br><br>
      </fieldset>

      <br>

      <fieldset>
        <legend>Offre</legend>
        <label>Places* : <input type="number" name="nb_place" required min="1"></label><br><br>
        <label>Prix par personne (crédits) : <input type="number" name="prix_personne" min="0"></label><br><br>

        <label>Véhicule* :
          <select name="id_voiture" required>
            <?php foreach ($vehicles as $v): ?>
              <option value="<?= (int)$v['id_voiture'] ?>">
                #<?= (int)$v['id_voiture'] ?> - <?= htmlspecialchars((string)($v['marque'] ?? '')) ?> <?= htmlspecialchars((string)$v['modele']) ?> (<?= htmlspecialchars((string)$v['energie']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </label><br><br>

        <p>
          Besoin d’un autre véhicule ?
          <a href="<?= BASE_URL ?>/account/vehicles">Ajouter un véhicule</a>
        </p>
      </fieldset>

      <button type="submit">Créer</button>
    </form>

  <?php endif; ?>

</body>
</html>
