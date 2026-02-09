<?php // views/auth/login.php (contenu uniquement) ?>

<div class="card">
  <h1>Connexion</h1>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars((string)$_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
    <br>
  <?php endif; ?>

  <form method="post" action="<?= BASE_URL ?>/login" class="form-grid">
    <label>
      Email
      <input type="email" name="email" required autocomplete="email">
    </label>

    <label>
      Mot de passe
      <input type="password" name="password" required autocomplete="current-password">
    </label>

    <button type="submit">Se connecter</button>
  </form>

  <br>

  <p class="muted">
    Pas encore de compte ?
    <a href="<?= BASE_URL ?>/register">Créer un compte</a>
  </p>
</div>
