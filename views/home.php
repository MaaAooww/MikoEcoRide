<?php // views/home.php (contenu uniquement) ?>

<div class="card">
  <img class="hero-img"
      src="<?= BASE_URL ?>/assets/images/bandeau.jpg"
      alt="EcoRide - covoiturage écologique">
</div>

<div class="card">
    <h2>Bienvenue sur EcoRide</h2>

    <p>
        <strong>EcoRide est votre solution de covoiturage écoresponsable.</strong>
    </p>

    <p>
        Notre mission est claire : réduire l’empreinte carbone des déplacements
        quotidiens tout en facilitant le partage de trajets entre particuliers.
    </p>

    <p>
        Nous sommes convaincus qu’un avenir plus durable repose sur des actions
        simples, accessibles et collectives.
    </p>

    <p>
        Notre plateforme met en relation conducteurs et passagers afin
        d’optimiser les trajets, limiter les émissions de CO₂ et favoriser
        une mobilité plus responsable, que vous habitiez en ville ou en zone rurale.
    </p>

    <p>
        <strong>Ensemble, adoptons une mobilité plus verte et roulons vers un futur plus propre.</strong>
    </p>
</div>

<br>

<div class="card">
  <h2>Rechercher un itinéraire</h2>

  <form method="get" action="<?= BASE_URL ?>/covoiturages" class="form-grid">
    <label>
      Départ
      <input name="depart" placeholder="Ex: Lyon">
    </label>

    <label>
      Arrivée
      <input name="arrivee" placeholder="Ex: Paris">
    </label>

    <label>
      Date
      <input type="date" name="date">
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
