<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Inscription - EcoRide</title>
</head>
<body>
  <h1>Inscription</h1>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><strong><?= htmlspecialchars($error) ?></strong></p>
  <?php endif; ?>

  <form method="post" action="<?= BASE_URL ?>/register">
    <label>Nom* <input name="nom" required></label><br>
    <label>Prénom* <input name="prenom" required></label><br>
    <label>Pseudo* <input name="pseudo" required></label><br>
    <label>Email* <input type="email" name="email" required></label><br>

    <label>Mot de passe* <input type="password" name="password" required></label><br>
    <label>Confirmer* <input type="password" name="password2" required></label><br>

    <label>Téléphone <input name="telephone"></label><br>
    <label>Adresse <input name="adresse"></label><br>
    <label>Date de naissance <input name="date_naissance" placeholder="YYYY-MM-DD"></label><br>
    <label>Photo (nom de fichier) <input name="photo" placeholder="ex: moi.jpg"></label><br>

    <button type="submit">Créer mon compte</button>
  </form>

  <p><a href="/">← Accueil</a> | <a href="/login">Connexion</a></p>
</body>
</html>
