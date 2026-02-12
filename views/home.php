<?php // views/home.php (contenu uniquement) ?>

<div class="card">
  <img class="hero-img"
      src="<?= BASE_URL ?>/assets/images/bandeau.jpg"
      alt="EcoRide - covoiturage écologique">
</div>

<br>

<div class="card">
  <h2>Rechercher un itinéraire</h2>

  <form method="get" action="<?= BASE_URL ?>/covoiturages" class="form-grid">
    <label>
      Départ
      <input name="depart" required>
    </label>

    <label>
      Arrivée
      <input name="arrivee" required>
    </label>

    <label>
      Date
      <input type="date" name="date" required>
    </label>

    <label>
      Prix max (crédits)
      <input type="number" name="prixMax" min="0">
    </label>

    <label class="checkbox" for="eco">
      <span>Voyage écologique uniquement (électrique)</span>
      <input id="eco" type="checkbox" name="eco" value="1">
    </label>

    <button type="submit">Rechercher</button>
  </form>
</div>
