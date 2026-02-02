<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mes véhicules - EcoRide</title>
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

  <h1>Mes véhicules</h1>

  <?php if ($error): ?>
    <p style="color:red;"><strong><?= htmlspecialchars((string)$error) ?></strong></p>
  <?php endif; ?>

  <h2>Liste</h2>

  <?php if (empty($vehicles)): ?>
    <p>Aucun véhicule enregistré.</p>
  <?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Marque</th>
          <th>Modèle</th>
          <th>Immatriculation</th>
          <th>Energie</th>
          <th>Couleur</th>
          <th>Date 1ère immat.</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vehicles as $v): ?>
          <tr>
            <td><?= (int)$v['id_voiture'] ?></td>
            <td><?= htmlspecialchars((string)($v['marque'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)$v['modele']) ?></td>
            <td><?= htmlspecialchars((string)$v['immatriculation']) ?></td>
            <td><?= htmlspecialchars((string)$v['energie']) ?></td>
            <td><?= htmlspecialchars((string)$v['couleur']) ?></td>
            <td><?= htmlspecialchars((string)$v['date_premiere_immatriculation']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <hr>

  <h2>Ajouter un véhicule</h2>

  <form method="post" action="<?= BASE_URL ?>/account/vehicles">
    <label>Marque :
      <select name="id_marque">
        <option value="0">-- (optionnel) --</option>
        <?php foreach ($marques as $m): ?>
          <option value="<?= (int)$m['id_marque'] ?>"><?= htmlspecialchars((string)$m['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>Modèle* : <input name="modele" required></label><br>
    <label>Immatriculation* : <input name="immatriculation" required></label><br>
    <label>Energie* : <input name="energie" required placeholder="electrique, essence, diesel..."></label><br>
    <label>Couleur : <input name="couleur"></label><br>
    <label>Date 1ère immat. : <input name="date_premiere_immatriculation" placeholder="YYYY-MM-DD"></label><br><br>

    <button type="submit">Ajouter</button>
  </form>

</body>
</html>
