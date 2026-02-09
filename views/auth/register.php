<?php // views/auth/register.php (contenu uniquement) ?>

<div class="card">
  <h1>Créer un compte</h1>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars((string)$_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
    <br>
  <?php endif; ?>

  <form method="post" action="<?= BASE_URL ?>/register" class="form-grid">
    <label>
      Pseudo
      <input type="text" name="pseudo" required autocomplete="nickname">
    </label>

    <label>
      Email
      <input type="email" name="email" required autocomplete="email">
    </label>

    <label>
      Mot de passe
      <input type="password" name="password" required autocomplete="new-password">
      <span class="muted">Minimum : 8 caractères (recommandé : lettres + chiffres).</span>
    </label>

    <button type="submit">Créer mon compte</button>
  </form>

  <br>

  <p class="muted">
    Déjà un compte ?
    <a href="<?= BASE_URL ?>/login">Se connecter</a>
  </p>
</div>
