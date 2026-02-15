<?php
// views/layout.php
// Convention B1 : le contrôleur définit $title et $viewFile, puis require ce layout.

$title = $title ?? "EcoRide";

if (!isset($viewFile) || !is_string($viewFile)) {
    throw new RuntimeException("layout.php: \$viewFile manquant.");
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars((string)$title) ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="container">
    <nav class="nav" aria-label="Navigation principale">
      <div class="nav-left">
        <a href="<?= BASE_URL ?>/"><strong>EcoRide</strong></a>
        <a href="<?= BASE_URL ?>/covoiturages">Covoiturages</a>
        <a href="<?= BASE_URL ?>/contact">Contact</a>
      </div>

      <div class="nav-right">
        <?php if (isset($_SESSION['user'])): ?>
          <span class="badge">
            Connecté : <strong><?= htmlspecialchars((string)$_SESSION['user']['pseudo']) ?></strong>
          </span>
          <a href="<?= BASE_URL ?>/account">Mon compte</a>

          <?php

          // Liens contextuels selon rôles (EMPLOYE / ADMIN)
          $isEmploye = false;
          $isAdmin = false;

          try {
              $userRepo = new UtilisateurRepository();
              $id = (int)$_SESSION['user']['id_utilisateur'];

              $isEmploye = $userRepo->hasRole($id, 'EMPLOYE');
              $isAdmin = $userRepo->hasRole($id, 'ADMIN') || $userRepo->hasRole($id, 'ADMINISTRATEUR');
          } catch (Throwable $e) {
              // en cas de souci DB, on n'affiche pas les liens
          }
        ?>

        <?php if ($isEmploye): ?>
          <a href="<?= BASE_URL ?>/employe/incidents">Espace employé</a>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
          <a href="<?= BASE_URL ?>/admin">Admin</a>
        <?php endif; ?>

          <?php
            // Afficher le lien Admin uniquement si l'utilisateur a le rôle ADMIN
            $isAdmin = false;
            try {
                $userRepo = new UtilisateurRepository();
                $id = (int)$_SESSION['user']['id_utilisateur'];
                $isAdmin = $userRepo->hasRole($id, 'ADMIN') || $userRepo->hasRole($id, 'ADMINISTRATEUR');
            } catch (Throwable $e) {
                $isAdmin = false;
            }
          ?>

          <?php if ($isAdmin): ?>
            <a href="<?= BASE_URL ?>/admin">Admin</a>
          <?php endif; ?>

          <form method="post" action="<?= BASE_URL ?>/logout" style="display:inline;">
            <button type="submit">Déconnexion</button>
          </form>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/login">Connexion</a>
          <a href="<?= BASE_URL ?>/register">Inscription</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<main class="main">
  <div class="container">
    <?php require $viewFile; ?>
  </div>
</main>

<footer class="footer">
  <div class="container">
    <p>
      Contact :
      <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a>
      &nbsp;|&nbsp;
      <a href="<?= BASE_URL ?>/mentions-legales">Mentions légales</a>
    </p>
  </div>
</footer>

</body>
</html>
